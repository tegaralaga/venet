<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 22:43
 */

namespace App\Helpers;

/**
 * Class TimeHelper
 * @package App\Helpers
 */
class TimeHelper
{
    /**
     * @return float
     */
    public static function server_elapsed_time()
    {
        $elapsed = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        return floor($elapsed * 1000);
    }
}