<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 01:01
 */

namespace App\Helpers;

use App\Models\KelurahanModel;
use App\Helpers\RedisHelper as RH;
use App\Models\VenueContactModel;
use App\Models\VenueModel;

/**
 * Class VenueHelper
 * @package App\Helpers
 */
class VenueHelper
{
    /**
     * @param int $kelurahan
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetAreaByKelurahan(int $kelurahan, $object = false, $refresh = false)
    {
        $result = [
            'kelurahan' => null,
            'kecamatan' => null,
            'kota' => null,
            'provinsi' => null,
            'negara' => null,
        ];
        $redis_key = 'area:by:kelurahan:' . $kelurahan;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $area = KelurahanModel::select('tbl_kelurahan.kel_name', 'tbl_kecamatan.kec_name', 'tbl_kota.kot_name', 'tbl_provinsi.pro_name', 'tbl_negara.neg_name')
                ->join('tbl_kecamatan', 'tbl_kelurahan.kel_kec_id', '=', 'tbl_kecamatan.kec_id')
                ->join('tbl_kota', 'tbl_kecamatan.kec_kot_id', '=', 'tbl_kota.kot_id')
                ->join('tbl_provinsi', 'tbl_kota.kot_pro_id', '=', 'tbl_provinsi.pro_id')
                ->join('tbl_negara', 'tbl_provinsi.pro_neg_id', '=', 'tbl_negara.neg_id')
                ->where('tbl_kelurahan.kel_id', $kelurahan)
                ->first();
            if (!($area == null)) {
                $result = [
                    'kelurahan' => $area->kel_name,
                    'kecamatan' => $area->kec_name,
                    'kota' => $area->kot_name,
                    'provinsi' => $area->pro_name,
                    'negara' => $area->neg_name,
                ];
                RH::Set($redis_key, json_encode($result), RH::WEEK);
            }
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
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
    public static function GetVenueData(int $id, $object = false, $refresh = false, $with_parent = false) {
        $result = [];
        $redis_key = 'venue:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = VenueModel::select(\DB::raw('tbl_venue.*'), 'tbl_venue_type.vty_name')
                ->join('tbl_venue_type', 'tbl_venue.ven_vty_id', '=', 'tbl_venue_type.vty_id')
                ->find($id);
            if (!($select == null)) {
                $parent = ($select->ven_parent == 0) ? null : (($with_parent) ? self::GetVenueData($select->ven_parent, $object, $refresh, $with_parent) : ['id' => $select->ven_parent]);
                $contacts = self::GetVenueContact($select->ven_id, $object, $refresh);
                $result = [
                    'id' => (int)$select->ven_id,
                    'name' => $select->ven_name,
                    'description' => $select->ven_description,
                    'capacity' => $select->ven_capacity,
                    'location' => [
                        'address' => $select->ven_address,
                        'visibility' => $select->ven_location_type,
                        'type' => $select->vty_name,
                        'coordinate' => [
                            'latitude' => $select->ven_coordinate->getLat(),
                            'longitude' => $select->ven_coordinate->getLng(),
                        ],
                        'area' => self::GetAreaByKelurahan($select->ven_kel_id, $object, $refresh)
                    ],
                    'contacts' => $contacts,
                    'parent' => $parent,
                ];
                $save_to_redis = $result;
                if (!($parent == null)) {
                    unset($save_to_redis['parent']);
                    $save_to_redis['parent'] = ['id' => $result['parent']['id']];
                }
                $save_to_redis['location']['area'] = $select->ven_kel_id;
                $save_to_redis['contacts'] = [];
                RH::Set($redis_key, json_encode($save_to_redis), RH::WEEK);
            } else {
                $result = null;
            }
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $result['location']['area'] = self::GetAreaByKelurahan($result['location']['area'], $object, $refresh);
                $result['parent'] = ($result['parent'] == null) ? null : (($with_parent) ? self::GetVenueData($result['parent']['id'], $object, $refresh, $with_parent) : ['id' => $result['parent']['id']]);
                $result['contacts'] = self::GetVenueContact($result['id'], $object, $refresh);
            }
        }
        if (!($with_parent)) {
            $result['parent'] = null;
        }
        return (($object) ? (object)$result : $result);
    }

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetVenueContact(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'venue:contact:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = VenueContactModel::select('vco_type', 'vco_value', 'vco_description')->where('vco_ven_id', $id)->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $type = null;
                    switch ($row->vco_type) {
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
                        'value' => $row->vco_value,
                        'description' => $row->vco_description,
                    ];
                }
            }
            RH::Set($redis_key, json_encode($result), RH::WEEK);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
            }
        }
        return (($object) ? (object)$result : $result);
    }
}