<?php
namespace RicoGoh\Timezones;

use Illuminate\Support\Facades\Facade;

class Timezones extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'timezones';
    }
}
