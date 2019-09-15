<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 20:05
 */

namespace App\Helpers;

use App\Models\OrganizerContactModel;
use App\Helpers\RedisHelper as RH;
use App\Models\OrganizerModel;

class OrganizerHelper
{
    const EXPIRE = RH::WEEK;

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetOrganizerData(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'organizer:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = OrganizerModel::select('org_name', 'org_description')->find($id);
            if (!($select == null)) {
                $result['name'] = $select->org_name;
                $result['description'] = $select->org_description;
                $result['contacts'] = self::GetOrganizerContact($id, $object, $refresh);
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
                $result['contacts'] = self::GetOrganizerContact($id, $object, $refresh);
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
    public static function GetOrganizerContact(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'organizer:contact:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = OrganizerContactModel::select('oco_type', 'oco_value', 'oco_description')->where('oco_org_id', $id)->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $type = null;
                    switch ($row->oco_type) {
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
                        'value' => $row->oco_value,
                        'description' => $row->oco_description,
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