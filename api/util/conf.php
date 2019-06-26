<?php
namespace util;
class conf
{
    public static $conf = [

    ];

    public static function getConf($key)
    {
        $conf = self::$conf;
        return $conf[$key];
    }
}
