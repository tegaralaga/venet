<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 00:59
 */

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\CustomerContactModel;
use App\Models\CustomerModel;
use App\Models\EventTicketDetailModel;
use App\Models\EventTicketQuoteHistoryModel;
use App\Models\TransactionDetailModel;
use App\Models\TransactionDetailTicketModel;
use App\Models\TransactionModel;
use App\Traits\VenetTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\TicketHelper as TH;
use App\Helpers\RedisHelper as RH;
use App\Helpers\EventHelper as EH;
use App\Helpers\TransactionHelper as TIH;

class TransactionController extends Controller
{

    use VenetTrait;

    public function __construct()
    {
    }

    public function transaction_info(Request $request, $id) {
        $id = $this->decode($id);
        $this->success = true;
        $this->data = TIH::GetTransaction($id);
        return $this->json();
    }

    public function purchase(Request $request) {
        $rules = [
            'customer' => 'required|exists:tbl_customer,cus_id',
            'ticket' => 'required|array|distinct',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $this->message = $validation->errors();
        } else {
            $customer = CustomerModel::select('cus_id', 'cus_kel_id', 'cus_address')->find($request['customer']);
            if ($customer->cus_kel_id == 0 || blank($customer->cus_address)) {
                $this->message = 'Please Complete Your Address Information For Ticket Shipping';
            } else {
                $customer_contacts = CustomerContactModel::select('cco_type', 'cco_value')
                    ->where('cco_cus_id', $request['customer'])
                    ->get();
                $number = false;
                $email = false;
                foreach ($customer_contacts as $customer_contact) {
                    $number_array = ['PHONE_NUMBER', 'MOBILE_NUMBER'];
                    $number_found = false;
                    $email_found = false;
                    if (in_array($customer_contact['cco_type'], $number_array)) {
                        $number_found = true;
                    } else if ($customer_contact['cco_type'] == 'EMAIL') {
                        $email_found = true;
                    }
                    if ($number_found) {
                        if (!$number) {
                            $number_replace = str_replace(' ', '', $customer_contact['cco_value']);
                            $number_replace = str_replace('-', '', $number_replace);
                            $number_replace = str_replace('+', '', $number_replace);
                            if (is_numeric($number_replace)) {
                                $number = true;
                            }
                        }
                    } else if ($email_found) {
                        if (!($email)) {
                            if (filter_var($customer_contact['cco_value'], FILTER_VALIDATE_EMAIL)) {
                                $email = true;
                            }
                        }
                    }
                    if ($number && $email) {
                        break;
                    }
                }
                if (($number == false) || ($email == false)) {
                    $this->message = 'Please Complete Your Contact Information [Phone/Mobile Number and E-Mail] For Ticket Information Notification';
                } else {
                    $ticket_length = count($request->ticket);
                    $input = $request->all();
                    $tickets = [];
                    foreach ($input['ticket'] as $row) {
                        $tickets[] = $this->decode($row);
                    }
                    $input['ticket'] = $tickets;
                    $rules = [
                        'ticket.*' => 'required|exists:tbl_event_ticket_detail,etd_id',
                        'quantity' => "required|array|min:{$ticket_length}",
                        'quantity.*' => 'required|numeric'
                    ];
                    $validation = Validator::make($input, $rules);
                    if ($validation->fails()) {
                        $this->message = $validation->errors();
                    } else {
                        $ticket_quote = [];
                        $continue = true;
                        $failed = [];
                        for ($i = 0; $i < $ticket_length; $i++) {
                            $quantity = $input['quantity'][$i];
                            $quote = TH::GetTicketAvailable($input['ticket'][$i]);
                            if ($quote < $quantity) {
                                $failed = [
                                    'id' => $this->encode($input['ticket'][$i]),
                                    'quote' => $quote,
                                    'quantity' => $quantity,
                                ];
                                $continue = false;
                                break;
                            }
                            $redis_key = 'ticket:quote:' . $input['ticket'][$i];
                            $ticket_quote[] = [
                                'id' => $input['ticket'][$i],
                                'key' => $redis_key,
                                'quote' => $quote,
                                'quantity' => $quantity,
                            ];
                        }
                        if (!($continue)) {
                            $this->message = 'Out Of Stock';
                            $this->data = [
                                'ticket' => $failed['id'],
                                'quantity' => (int)$failed['quantity'],
                                'quote' => $failed['quote'],
                            ];
                        } else {
                            $before = [];
                            foreach ($ticket_quote as $row) {
                                $before[] = $row;
                                if (!(RH::DecreaseTicket($row['key'], $row['quantity']))) {
                                    $failed = $row;
                                    $continue = false;
                                    break;
                                }
                            }
                            if (!($continue)) {
                                foreach ($before as $row) {
                                    RH::IncreaseTicket($row['key'], $row['quantity']);
                                }
                                $this->message = 'Out Of Stock';
                                $this->data = [
                                    'ticket' => $this->encode($failed['id']),
                                    'quantity' => (int)$failed['quantity'],
                                    'quote' => $failed['quote'],
                                ];
                            } else {
                                $continue = true;
                                $ticket_data = [];
                                $event_id = null;
                                $error_ticket = [];
                                foreach ($ticket_quote as $row) {
                                    $error_ticket = $row;
                                    $select = EventTicketDetailModel::select('tbl_event_ticket_detail.etd_price', 'tbl_event_ticket_detail.etd_id', 'tbl_event_ticket.eti_date_start', 'tbl_event_ticket.eti_date_end', 'tbl_event.eve_published', 'tbl_event.eve_id')
                                        ->join('tbl_event_ticket', 'tbl_event_ticket_detail.etd_eti_id', '=', 'tbl_event_ticket.eti_id')
                                        ->join('tbl_event', 'tbl_event_ticket.eti_eve_id', 'tbl_event.eve_id')
                                        ->where('tbl_event_ticket_detail.etd_id', $row['id'])->first();
                                    if ($select == null) {
                                        $continue = false;
                                        $this->message = 'Ticket Not Found';
                                        break;
                                    } else {
                                        if ($event_id == null) {
                                            $event_id = $select->eve_id;
                                        }
                                        if (!($event_id == $select->eve_id)) {
                                            $continue = false;
                                            $this->message = 'Ticket Must Be From Same Event';
                                            break;
                                        } else {
                                            if (!($select->eve_published)) {
                                                $continue = false;
                                                $this->message = 'Event Not Found';
                                                break;
                                            } else {
                                                $date_start = Carbon::parse($select->eti_date_start, env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                                                $date_end = Carbon::parse($select->eti_date_end, env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                                                $now = Carbon::now(env('APP_LOCAL_TIMEZONE', 'Asia/Jakarta'));
                                                if (!($now->greaterThanOrEqualTo($date_start) and $now->lessThanOrEqualTo($date_end))) {
                                                    $continue = false;
                                                    $this->message = 'Ticket Unavailable or Already Expired';
                                                    break;
                                                } else {
                                                    $row['price'] = $select->etd_price;
                                                    $row['etd_id'] = $select->etd_id;
                                                }
                                            }
                                        }
                                    }
                                    $ticket_data[] = $row;
                                }
                                $rollback_quotation_redis = false;
                                if ($continue) {
                                    EH::GetEventTax($event_id);
                                    $event_tax = RH::Get('event:tax:' . $event_id);
                                    \DB::beginTransaction();
                                    try {
                                        $transaction = new TransactionModel([
                                            'tra_cus_id' => $request->customer,
                                            'tra_comment' => ((blank($request->comment)) ? null : $request->comment),
                                            'tra_status' => 'SUCCESS',
                                        ]);
                                        $transaction->save();
                                        foreach ($ticket_data as $row) {
                                            $total = (int)($row['price'] + ($row['price']*($event_tax/100))) * $row['quantity'];
                                            $transaction_detail = new TransactionDetailModel([
                                                'tde_tra_id' => $transaction->tra_id,
                                                'tde_etd_id' => $row['id'],
                                                'tde_price' => $row['price'],
                                                'tde_tax' => $event_tax,
                                                'tde_quantity' => $row['quantity'],
                                                'tde_total' => $total,
                                            ]);
                                            $transaction_detail->save();
                                            $insert = [];
                                            for($i = 0; $i < $row['quantity']; $i++) {
                                                $insert[] = [
                                                    'ttd_tde_id' => $transaction_detail->tde_id,
                                                ];
                                            }
                                            TransactionDetailTicketModel::insert($insert);
                                            $event_ticket_quote_history = EventTicketQuoteHistoryModel::select('eth_total')
                                                ->where('eth_etd_id', $row['etd_id'])
                                                ->orderBy('eth_id', 'desc')
                                                ->first();
                                            if ($event_ticket_quote_history == null) {
                                                $this->message = 'Ticket Quote History Not Found';
                                                $rollback_quotation_redis = true;
                                                break;
                                            } else {
                                                $event_ticket_quote_history = new EventTicketQuoteHistoryModel([
                                                    'eth_etd_id' => $row['etd_id'],
                                                    'eth_type' => 'DEBET',
                                                    'eth_note' => 'TRANSACTION',
                                                    'eth_value' => $row['quantity'],
                                                    'eth_total' => ($event_ticket_quote_history->eth_total - $row['quantity']),
                                                    'eth_comment' => "TRANSACTION [$transaction->tra_id] | CUSTOMER [$customer->cus_id]",
                                                ]);
                                                $event_ticket_quote_history->save();
                                            }
                                        }
                                        if (!($rollback_quotation_redis)) {
                                            \DB::commit();
                                            try {
                                                $this->success = true;
                                                $this->data = TIH::GetTransaction($transaction->tra_id);
                                            } catch (\Exception $e) {
                                                $rollback_quotation_redis = true;
                                                $this->message = $e->getMessage();
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        \DB::rollBack();
                                        $rollback_quotation_redis = true;
                                        $this->message = $e->getMessage();
                                    }
                                } else {
                                    $this->data = [
                                        'ticket' => $this->encode($error_ticket['id']),
                                    ];
                                    $rollback_quotation_redis = true;
                                }
                                if ($rollback_quotation_redis) {
                                    foreach ($ticket_quote as $row) {
                                        RH::IncreaseTicket($row['key'], $row['quantity']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->json();
    }

}