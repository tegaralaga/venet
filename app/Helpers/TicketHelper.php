<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 23:24
 */

namespace App\Helpers;

use App\Helpers\RedisHelper as RH;
use App\Models\EventTicketDetailModel;
use App\Helpers\EventHelper as EH;
use App\Helpers\HashHelper as HH;
use App\Models\EventTicketModel;
use Carbon\Carbon;

class TicketHelper
{
    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetTicket(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'ticket:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventTicketModel::select('eti_id', 'eti_name', 'eti_description', 'eti_date_start', 'eti_date_end')
                ->where('eti_eve_id', $id)
                ->get();
            $save_to_redis = [];
            if (!($select == null)) {
                foreach ($select as $row) {
                    $date_start = Carbon::parse($row->eti_date_start, env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                    $date_end = Carbon::parse($row->eti_date_end, env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                    $date_now = Carbon::now(env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                    $status = (($date_now->greaterThanOrEqualTo($date_start) && $date_now->lessThanOrEqualTo($date_end)) ? 'READY' : 'UNAVAILABLE');
                    $data = [
                        'id' => HH::encode($row->eti_id),
                        'name' => $row->eti_name,
                        'description' => $row->eti_description,
                        'chrono' => [
                            'start' => $row->eti_date_start,
                            'end' => $row->eti_date_end,
                        ],
                        'detail' => self::GetTicketDetail($row->eti_id, $id, $object, $refresh),
                        'status' => $status,
                    ];
                    $result[] = $data;
                    $temp = $data;
                    unset($temp['status']);
                    $save_to_redis[] = $temp;
                }
            }
            RH::Set($redis_key, json_encode($save_to_redis), RH::WEEK);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $temp = [];
                foreach ($result as $row) {
                    $date_start = Carbon::parse($row['chrono']['start']);
                    $date_end = Carbon::parse($row['chrono']['end']);
                    $date_now = Carbon::now(env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                    $status = (($date_now->greaterThanOrEqualTo($date_start) && $date_now->lessThanOrEqualTo($date_end)) ? 'READY' : 'UNAVAILABLE');
                    $row['detail'] = self::GetTicketDetail(HH::decode($row['id']), $id, $object, $refresh);
                    $row['status'] = $status;
                    $temp[] = $row;
                }
                $result = $temp;
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
    public static function GetTicketAvailable(int $id, $object = false, $refresh = false) {
        $result = 0;
        $redis_key = 'ticket:quote:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = \DB::select("SELECT eth_total FROM tbl_event_ticket_quote_history WHERE eth_etd_id = ? ORDER BY eth_id DESC;", [$id]);
            if (!($select == null)) {
                $result = $select->eth_total;
            }
            RH::Set($redis_key, json_encode($result), RH::WEEK);
        } else {
            $result = (int)$cache;
        }
        return (($object) ? (object)$result : $result);
    }

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetTicketDetail(int $id, $event_id, $object = false, $refresh = false) {
        EH::GetEventTax($event_id);
        $event_tax = RH::Get('event:tax:' . $event_id);
        $result = [];
        $redis_key = 'ticket:detail:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = EventTicketDetailModel::select('etd_id', 'etd_name', 'etd_description', 'etd_price', 'etd_quote')
                ->where('etd_eti_id', $id)
                ->get();
            $safe_to_redis = [];
            if (!($select == null)) {
                foreach ($select as $row) {
                    $data = [
                        'id' => HH::encode($row->etd_id),
                        'name' => $row->etd_name,
                        'description' => $row->etd_description,
                        'price' => [
                          'before' => $row->etd_price,
                          'tax' => $event_tax . '%',
                          'after' => (int)($row->etd_price + ($row->etd_price * ($event_tax/100))),
                        ],
                        'quote' => [
                            'available' => self::GetTicketAvailable($row->etd_id),
                            'original' => $row->etd_quote,
                        ],
                    ];
                    $status = (($data['quote']['available'] < 1) ? 'OUT_OF_STOCK' : 'AVAILABLE');
                    $data['status'] = $status;
                    $temp = $data;
                    unset($temp['price']['tax']);
                    unset($temp['price']['after']);
                    unset($temp['quote']['available']);
                    $safe_to_redis[] = $temp;
                    $result[] = $data;
                }
            }
            RH::Set($redis_key, json_encode($safe_to_redis), RH::WEEK);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $temp = [];
                foreach ($result as $row) {
                    $row['price']['tax'] = $event_tax . '%';
                    $row['price']['after'] = (int)($row['price']['before'] + ($row['price']['before'] * ($event_tax/100)));
                    $row['quote']['available'] = self::GetTicketAvailable(HH::decode($row['id']));
                    $status = (($row['quote']['available'] < 1) ? 'OUT_OF_STOCK' : 'AVAILABLE');
                    $row['status'] = $status;
                    $temp[] = $row;
                }
                $result = $temp;

            }
        }
        return (($object) ? (object)$result : $result);
    }
}