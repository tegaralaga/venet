<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 20:05
 */

namespace App\Helpers;

use App\Models\LineUpContactModel;
use App\Helpers\RedisHelper as RH;
use App\Models\LineUpModel;

class LineUpHelper
{
    const EXPIRE = RH::WEEK;

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetLineUpData(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'line_up:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = LineUpModel::select('lin_name', 'lin_description', 'lty_name')
                ->join('tbl_line_up_type', 'tbl_line_up.lin_lty_id', '=', 'tbl_line_up_type.lty_id')
                ->where('lin_id', $id)
                ->first();
            if (!($select == null)) {
                $result['type'] = $select->lty_name;
                $result['name'] = $select->lin_name;
                $result['description'] = $select->lin_description;
                $result['contacts'] = self::GetLineUpContact($id, $object, $refresh);
                $save_to_redis = $result;
                unset($save_to_redis['contacts']);
                RH::Set($redis_key, json_encode($save_to_redis), self::EXPIRE);
            } else {
                $result = null;
            }
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $result['contacts'] = self::GetLineUpContact($id, $object, $refresh);
            }
        }
        return (($object) ? (object)$result : $result);
    }

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetLineUpContact(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'line_up:contact:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = LineUpContactModel::select('lco_type', 'lco_value', 'lco_description')->where('lco_lin_id', $id)->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $type = null;
                    switch ($row->lco_type) {
                        case 'PHONE_NUMBER':
                            $type = 'Phone Number';
                            break;
                        case 'MOBILE_NUMBER':
                            $type = 'Mobile Number';
                            break;
                        case 'FAX_NUMBER':
                            $type = 'FAX NUMBER';
                            break;
                        case 'TWITTER':
                            $type = 'Twitter';
                            break;
                        case 'FACEBOOK':
                            $type = 'Facebook';
                            break;
                        case 'INSTAGRAM':
                            $type = 'Instagram';
                            break;
                        case 'WEBSITE':
                            $type = 'Website';
                            break;
                    }
                    $result[] = [
                        'type' => $type,
                        'value' => $row->lco_value,
                        'description' => $row->lco_description,
                    ];
                }
            }
            RH::Set($redis_key, json_encode($result), self::EXPIRE);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
            }
        }
        return (($object) ? (object)$result : $result);
    }
}