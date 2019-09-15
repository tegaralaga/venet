<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 20:01
 */

namespace App\Helpers;

use LaravelHashids\Facades\Hashids;

class HashHelper
{

    public static function encode($value) {
        return Hashids::encode($value);
    }

    public static function decode($value) {
        $decoded = Hashids::decode($value);
        if (count($decoded) == 0)
            return 0;
        return $decoded[0];
    }

}