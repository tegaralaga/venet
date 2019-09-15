<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 23:25
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Redis;

/**
 * Class RedisHelper
 * @package App\Helpers
 */
class RedisHelper
{
    const REDIS_PREFIX = 'venet:';
    const SECOND = 1;
    const MINUTE = self::SECOND * 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 24;
    const WEEK = self::DAY * 7;
    const MONTH = self::DAY * 30;

    /**
     * @param $key
     * @return mixed
     */
    public static function Get($key)
    {
        self::setKey($key);
        $value = Redis::get($key);
        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @param int $ttl
     */
    public static function Set($key, $value, $ttl = 0)
    {
        self::setKey($key);
        Redis::set($key, $value);
        if ($ttl > 0) {
            Redis::command('EXPIRE', [$key, $ttl]);
        }
    }

    public static function TTL($key)
    {
        self::setKey($key);
        $ttl = Redis::command('TTL', [$key]);
        if ($ttl < 1) {
            $ttl = 0;
        }
        return $ttl;
    }

    public static function IncreaseTicket($key, $value) {
        self::setKey($key);
        $quote = Redis::get($key);
        if ($quote == null) {
            $quote = 0;
        }
        $quote = $quote + $value;
        $ttl = Redis::command('TTL', [$key]);
        if ($ttl < 1) {
            $ttl = self::WEEK;
        }
        Redis::set($key, $quote);
        Redis::command('EXPIRE', [$key, $ttl]);
        return true;
    }

    public static function DecreaseTicket($key, $value) {
        self::setKey($key);
        $quote = Redis::get($key);
        if (!($quote == null)) {
            if ($quote >= $value) {
                $quote = $quote - $value;
                $ttl = Redis::command('TTL', [$key]);
                if ($ttl < 1) {
                    $ttl = self::WEEK;
                }
                Redis::set($key, $quote);
                Redis::command('EXPIRE', [$key, $ttl]);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public static function Incr($key) {
        self::setKey($key);
        Redis::command('INCR', [$key]);
    }

    public static function Decr($key) {
        self::setKey($key);
        Redis::command('DECR', [$key]);
    }

    public static function setKey(&$key) {
        $key = self::REDIS_PREFIX . $key;
    }

}