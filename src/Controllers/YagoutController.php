<?php

namespace MikiBabi\YagoutPay\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Log;

class YagoutController extends Controller
{
    public function decrypt($crypt)
{
    $iv = "0123456789abcdef";
    // Use configured merchant key to match encryption used during initiate
    $key = \config('yagoutpay.merchant_key');
    $decodedCrypt = is_string($crypt) ? base64_decode($crypt, true) : $crypt;

    if ($decodedCrypt === false) {
        Log::error('Decryption failed: Invalid base64 input.', [
            'crypt' => $crypt,
            'key' => $key,
            'type' => $type
        ]);
        return false;
    }

    $decrypted = openssl_decrypt(
        $decodedCrypt,
        "AES-256-CBC",
        base64_decode($key),
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv
    );

    if ($decrypted === false) {
        Log::error('Decryption failed: openssl_decrypt returned false.', [
            'crypt' => base64_encode($decodedCrypt),
            'key' => $key,
            'type' => $type
        ]);
        return false;
    }

    $pad = ord($decrypted[strlen($decrypted) - 1]);
    if ($pad > strlen($decrypted)) {
        Log::error('Decryption failed: Padding is larger than decrypted string.', [
            'pad' => $pad,
            'decrypted_length' => strlen($decrypted)
        ]);
        return false;
    }

    // Check PKCS7 padding validity
    $paddingChar = $decrypted[strlen($decrypted) - 1];
    $paddingValid = strspn($decrypted, $paddingChar, strlen($decrypted) - $pad) == $pad;
    if (!$paddingValid) {
        Log::warning('Decryption warning: Invalid PKCS7 padding detected.', [
            'pad' => $pad,
            'decrypted' => base64_encode($decrypted)
        ]);
        // Optionally, you could return false here instead of continuing
    }

    $text = substr($decrypted, 0, -1 * $pad);

    Log::info('Decryption successful.', [
        'decrypted_text' => $text,
        'pad' => $pad
    ]);

    return $text;
}

    protected function parseCallback($rawData)
    {
        $parts = explode('|', $rawData);
        return [
            'gateway'        => $parts[0] ?? null,
            'merchant_id'    => $parts[1] ?? null,
            'order_id'       => $parts[2] ?? null,
            'amount'         => $parts[3] ?? null,
            'from_currency'  => $parts[4] ?? null,
            'to_currency'    => $parts[5] ?? null,
            'date'           => $parts[6] ?? null,
            'time'           => $parts[7] ?? null,
            'transaction_id' => $parts[8] ?? null,
            'auth_code'      => $parts[9] ?? null,
            'status_message' => $parts[10] ?? null,
            'error_code'     => $parts[11] ?? null,
            'error_message'  => $parts[12] ?? null,
            'final_amount'   => $parts[13] ?? null,
        ];
    }

    public function success(Request $request)
    {
     

        $crypt=$request->txn_response;
        $decrypted = $this->decrypt($crypt);
        $data = $this->parseCallback($decrypted);

        
        return $data;
        
    }

    public function failure(Request $request)
    {
        $crypt=$request->txn_response;
        $decrypted = $this->decrypt($crypt);
        $data = $this->parseCallback($decrypted);
        return $data;
    }
    public function test()
    {
        $data = [];
        return $data;
    }
}
