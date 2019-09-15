<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 04:27
 */

namespace App\Helpers;

use App\Helpers\RedisHelper as RH;
use App\Models\TransactionDetailModel;
use App\Models\TransactionDetailTicketModel;
use App\Helpers\HashHelper as HH;
use App\Models\TransactionModel;
use App\Helpers\EventHelper as EH;

class TransactionHelper
{

    const EXPIRE = RH::WEEK;

    /**
     * @param int $id
     * @param bool $object
     * @param bool $refresh
     * @return array|mixed|object
     */
    public static function GetTransactionDetailTicket(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'transaction:detail:ticket' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = TransactionDetailTicketModel::select('ttd_id', 'ttd_checked_in', 'ttd_check_in_datetime')
                ->where('ttd_tde_id', $id)
                ->get();
            if (!($select == null)) {
                foreach ($select as $row) {
                    $data = [
                        'id' => HH::encode($row->ttd_id),
                        'checkin' => [
                            'checked_in' => $row->ttd_checked_in,
                            'datetime' => $row->ttd_check_in_datetime,
                        ],
                    ];
                    $result[] = $data;
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
    public static function GetTransactionDetail(int $id, $object = false, $refresh = false) {
        $result = [];
        $redis_key = 'transaction:detail:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = TransactionDetailModel::select('tbl_transaction_detail.tde_id', 'tbl_transaction_detail.tde_price', 'tbl_transaction_detail.tde_tax', 'tbl_transaction_detail.tde_quantity', 'tbl_transaction_detail.tde_total', 'tbl_event_ticket_detail.etd_name', 'tbl_event_ticket_detail.etd_description')
                ->join('tbl_event_ticket_detail', 'tbl_transaction_detail.tde_etd_id', '=', 'tbl_event_ticket_detail.etd_id')
                ->where('tbl_transaction_detail.tde_tra_id', $id)
                ->get();
            $save_to_redis = [];
            if (!($select == null)) {
                foreach ($select as $row) {
                    $data = [
                        'id' => HH::encode($row->tde_id),
                        'info' => [
                            'name' => $row->etd_name,
                            'description' => $row->etd_description,
                        ],
                        'price' => [
                            'amount' => $row->tde_price,
                            'tax' => $row->tde_tax . '%',
                            'quantity' => $row->tde_quantity,
                            'total' => $row->tde_total,
                        ],
                        'ticket' => self::GetTransactionDetailTicket($row->tde_id)
                    ];
                    $result[] = $data;
                    unset($data['ticket']);
                    $save_to_redis[] = $data;
                }
            }
            RH::Set($redis_key, json_encode($save_to_redis), self::EXPIRE);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $temp = [];
                foreach ($result as $row) {
                    $row['ticket'] = self::GetTransactionDetailTicket(HH::decode($row['id']));
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
    public static function GetTransaction(int $id, $object = false, $refresh = false) {
        $result = [
            'id' => null,
            'comment' => null,
            'customer' => [],
            'detail' => [],
            'event' => [],
        ];
        $redis_key = 'transaction:' . $id;
        $cache = RH::Get($redis_key);
        if ($refresh) {
            $cache = null;
        }
        if ($cache == null) {
            $select = TransactionModel::select('tra_cus_id', 'tra_comment')->find($id);
            $save_to_redis = [];
            if (!($select == null)) {
                $event = TransactionDetailModel::select('tbl_event_ticket.eti_eve_id')
                    ->join('tbl_event_ticket_detail', 'tbl_transaction_detail.tde_etd_id', '=', 'tbl_event_ticket_detail.etd_id')
                    ->join('tbl_event_ticket', 'tbl_event_ticket_detail.etd_eti_id', '=', 'tbl_event_ticket.eti_id')
                    ->where('tbl_transaction_detail.tde_tra_id', $id)
                    ->orderBy('tbl_transaction_detail.tde_id', 'desc')
                    ->first();
                if (!($event == null)) {
                    $event = EH::GetEventData($event->eti_eve_id, $object, $refresh);
                    unset($event['ticket']);
                    $data = [
                        'id' => HH::encode($id),
                        'comment' => $select->tra_comment,
                        'customer' => [],
                        'detail' => self::GetTransactionDetail($id, $object, $refresh),
                        'event' => $event,
                    ];
                    $result = $data;
                    unset($data['customer']);
                    unset($data['detail']);
                    unset($data['event']);
                    $data['event']['id'] = HH::decode($result['event']['id']);
                    $save_to_redis = $data;
                }
            }
            RH::Set($redis_key, json_encode($save_to_redis), self::EXPIRE);
        } else {
            $cache = json_decode($cache, true);
            if (!(count($cache) == 0)) {
                $result = $cache;
                $event = EH::GetEventData($result['event']['id'], $object, $refresh);
                unset($event['ticket']);
                $result['event'] = $event;
                $result['customer'] = [];
                $result['detail'] = self::GetTransactionDetail($id, $object, $refresh);
            }
        }
        return (($object) ? (object)$result : $result);
    }

}