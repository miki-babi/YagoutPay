<?php

namespace MikiBabi\YagoutPay\Facades;

use Illuminate\Support\Facades\Facade;

class Yagout extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'yagout';
    }
}
