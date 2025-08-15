<?php


namespace MikiBabi\YagoutPay;

use Illuminate\Support\Facades\Log;

class Yagout
{


    public function encrypt($text, $key, $type)
    {
        $iv = "0123456789abcdef"; // Static IV [4]
        $size = 16;
        $pad = $size - (strlen($text) % $size);
        $padtext = $text . str_repeat(chr($pad), $pad);
        $crypt = openssl_encrypt($padtext, "AES-256-CBC", base64_decode($key), OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv); // [4]
        return base64_encode($crypt); // [4]
    }

    private function generateSha256Hash($input)
    { // [5]
        return hash('sha256', $input); // [5]
    }



    /**
     * Initiate payment (returns array with encrypted payload)
     */
    public function initiate(string $order_no, float $amount, array $cust_details, string $currency = 'ETB', string $txn_type = 'SALE', ?string $success_url = null, ?string $failure_url = null)
    {
        // dd('Initiating payment...');

        $merchantId = config('yagoutpay.merchant_id');
        $encryptionKey = config('yagoutpay.merchant_key');
        $postUrl = config('yagoutpay.payment_url');

        $success_url = $success_url ?? route('yagoutpay.success');
        $failure_url = $failure_url ?? route('yagoutpay.failure');

        $ag_id = "yagout";
        $channel = "WEB";

        $txn_details = implode('|', [
            $ag_id,
            $merchantId,
            $order_no,
            $amount,
            'ETH',
            'ETB',
            $txn_type,
            $success_url,
            $failure_url,
            $channel
        ]);

        $pg_details = implode('|', ["", "", "", ""]);
        $card_details = implode('|', ["", "", "", "", ""]);

        $cust_details_str = implode('|', [
            $cust_details['name'] ?? '',
            $cust_details['email'] ?? '',
            $cust_details['phone'] ?? '',
            $cust_details['extra'] ?? '',
            'Y'
        ]);

        $bill_details = implode('|', ["", "", "", "", ""]);
        $ship_details = implode('|', ["", "", "", "", "", "", ""]);
        $item_details = implode('|', ["", "", ""]);
        $upi_details = "";
        $other_details = implode('|', ["", "", "", "", ""]);


        $all_values = $txn_details . '~' . $pg_details . '~' . $card_details . '~' .
            $cust_details_str . '~' . $bill_details . '~' . $ship_details . '~' .
            $item_details . '~' . $upi_details . '~' . $other_details;


        $merchant_request_encrypted = $this->encrypt($all_values, $encryptionKey, 256);

        $hash_input = implode('~', [
            $merchantId,
            $order_no,
            $amount,
            'ETH',
            'ETB'
        ]);

        $sha256_hash = $this->generateSha256Hash($hash_input);
        $hash_value = $this->encrypt($sha256_hash, $encryptionKey, 256);

        Log::info('🔹 Hash Debug', [
            'hash_input' => $hash_input,
            'sha256_hash' => $sha256_hash,
            'hash_value_encrypted' => $hash_value
        ]);
        return view('yagoutpay::form', [
            'merchantId' => $merchantId,
            'merchant_request_encrypted' => $merchant_request_encrypted,
            'hash_value' => $hash_value,
            'postUrl' => $postUrl
        ]);
    }
}

