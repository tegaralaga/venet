<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 23:25
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    const REDIS_PREFIX = 'venet:';
    const MINUTE = 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 24;
    const WEEK = self::DAY * 7;
    const MONTH = self::DAY * 30;

    public static function Get($key)
    {
        self::setKey($key);
        $value = Redis::get($key);
        return $value;
    }

    public static function Set($key, $value, $ttl = 0)
    {
        self::setKey($key);
        Redis::set($key, $value);
        if ($ttl > 0) {
            Redis::command('EXPIRE', [$key, $ttl]);
        }
    }

    public static function setKey(&$key) {
        $key = self::REDIS_PREFIX . $key;
    }

}