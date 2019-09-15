<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 19:33
 */

namespace App\Helpers;

use App\Helpers\RedisHelper as RH;
use App\Models\EventContactModel;
use App\Models\EventLineUpModel;
use App\Models\EventModel;
use App\Models\EventRulesModel;
use App\Models\EventScheduleModel;
use App\Models\EventTagsModel;
use App\Helpers\LineUpHelper as LUH;
use App\Helpers\OrganizerHelper as OH;
use App\Helpers\HashHelper as HH;
use App\Helpers\VenueHelper as VH;
use App\Models\EventTaxModel;
use App\Helpers\TicketHelper as TH;

class EventHelper
{
    const EXPIRE = RH::WEEK;

    public static function UpdateEventData(array $event){
        $id = HH::decode($event['id']);
        $redis_key = 'event:' . $id;
        $ttl = RH::TTL($redis_key);
        unset($event['line_up']);
        unset($event['venue']);
        unset($event['organizer']);
        unset($event['tags']);
        unset($event['rules']);
        unset($event['chrono']['schedule']);
        RH::Set($redis_key, json_encode($event), $ttl);

    }

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetEventData(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $event = EventModel::select('eve_name', 'eve_ven_id', 'eve_org_id', 'ety_name', 'eve_description', 'eve_date_start', 'eve_date_end', 'eve_published')
                    ->join('tbl_event_type', 'tbl_event.eve_ety_id', '=', 'tbl_event_type.ety_id')
                    ->where('tbl_event.eve_id', $id)
                    ->first();
            if (!($event == null)) {
                $result = [
                    'id' => HH::encode($id),
                    'name' => $event->eve_name,
                    'description' => $event->eve_description,
                    'type' => $event->ety_name,
                    'venue' => VH::GetVenueData($event->eve_ven_id),
                    'chrono' => [
                        'date' => [
                            'start' => $event->eve_date_start,
                            'end' => $event->eve_date_end,
                        ],
                        'schedule' => self::GetEventSchedule($id, $object, $refresh),
                    ],
                    'line_up' => self::GetEventLineUp($id, $object, $refresh),
                    'organizer' => OH::GetOrganizerData($event->eve_org_id, $object, $refresh),
                    'tags' => self::GetEventTags($id, $object, $refresh),
                    'rules' => self::GetEventRules($id, $object, $refresh),
                    'taxes' => self::GetEventTax($id, $object, $refresh),
                    'ticket' => TH::GetTicket($id, $object, $refresh),
                    'published' => $event->eve_published,
                ];
                $save_to_redis = $result;
                unset($save_to_redis['line_up']);
                unset($save_to_redis['venue']);
                unset($save_to_redis['organizer']);
                unset($save_to_redis['tags']);
                unset($save_to_redis['rules']);
                unset($save_to_redis['taxes']);
                unset($save_to_redis['ticket']);
                unset($save_to_redis['chrono']['schedule']);
                RH::Set($redis_key, json_encode($save_to_redis), self::EXPIRE);
            } else {
                $result = null;
            }
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $id = HH::decode($result['id']);
                $result['venue'] = VH::GetVenueData($id, $object, $refresh);
                $result['line_up'] = self::GetEventLineUp($id, $object, $refresh);
                $result['organizer'] = OH::GetOrganizerData($id, $object, $refresh);
                $result['tags'] = self::GetEventTags($id, $object, $refresh);
                $result['rules'] = self::GetEventRules($id, $object, $refresh);
                $result['taxes'] = self::GetEventTax($id, $object, $refresh);
                $result['ticket'] = TH::GetTicket($id, $object, $refresh);
                $result['chrono']['schedule'] = self::GetEventSchedule($id, $object, $refresh);
            }
        }
        return (($object) ? json_decode(json_encode($result)) : $result);
    }

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetEventRules(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:rules:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventRulesModel::select('eru_text_id', 'eru_text_en')
                ->join('tbl_event_rule', 'tbl_event_rules.ers_eru_id', '=', 'tbl_event_rule.eru_id')
                ->where('tbl_event_rules.ers_eve_id', $id)
                ->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $result[] = [
                        'id' => $row->eru_text_id,
                        'en' => $row->eru_text_en,
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

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetEventSchedule(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:schedule:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventScheduleModel::select('esc_date_start', 'esc_date_end', 'esc_time_start', 'esc_time_end')
                ->where('tbl_event_schedule.esc_eve_id', $id)
                ->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $result[] = [
                        'date' => [
                            'start' => $row->esc_date_start,
                            'end' => $row->esc_date_end,
                        ],
                        'time' => [
                            'start' => $row->esc_time_start,
                            'end' => $row->esc_time_end,
                        ]
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

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetEventLineUp(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:line_up:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventLineUpModel::select('eli_lin_id')
                ->where('eli_eve_id', $id)
                ->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $result[] = LUH::GetLineUpData($row->eli_lin_id);
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

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetEventTags(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:tags:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventTagsModel::select('eta_name')
                ->join('tbl_event_tag', 'tbl_event_tags.ets_eta_id', '=', 'tbl_event_tag.eta_id')
                ->where('tbl_event_tags.ets_eve_id', $id)
                ->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $result[] = $row->eta_name;
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

    public static function GetEventTax(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key_tax_data = 'event:tax:data:' . $id;
        $redis_key_tax = 'event:tax:' . $id;
        $cache = RH::Get($redis_key_tax_data);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventTaxModel::select('etx_tax', 'etx_description')->where('etx_eve_id', $id)->get();
            if (!($select == null)) {
                $tax = 0;
                foreach ($select as $row) {
                    $result[] = [
                        'tax' => $row->etx_tax,
                        'description' => $row->etx_description,
                    ];
                    $tax = $tax + $row->etx_tax;
                }
            }
            RH::Set($redis_key_tax_data, json_encode($result), self::EXPIRE);
            RH::Set($redis_key_tax, $tax, self::EXPIRE);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $tax = 0;
                foreach($result as $row) {
                    $tax = $tax + $row['tax'];
                }
                $ttl = RH::TTL($redis_key_tax);
                if ($ttl == 0) {
                    $ttl = self::EXPIRE;
                }
                RH::Set($redis_key_tax, $tax, $ttl);
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
    public static function GetEventContact(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'event:contact:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventContactModel::select('eco_type', 'eco_value', 'eco_description')->where('eco_eve_id', $id)->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $type = null;
                    switch ($row->eco_type) {
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
                        'value' => $row->eco_value,
                        'description' => $row->eco_description,
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