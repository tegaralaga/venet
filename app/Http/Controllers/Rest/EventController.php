<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 17:50
 */

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\EventLineUpModel;
use App\Models\EventModel;
use App\Models\EventRulesModel;
use App\Models\EventScheduleModel;
use App\Models\EventTagModel;
use App\Models\EventTagsModel;
use App\Models\EventTaxModel;
use App\Models\EventTicketDetailModel;
use App\Models\EventTicketModel;
use App\Models\EventTicketQuoteHistoryModel;
use App\Traits\VenetTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\EventHelper as EH;
use App\Helpers\RedisHelper as RH;
use App\Helpers\TicketHelper as TH;

class EventController extends Controller
{
    use VenetTrait;

    public function __construct()
    {

    }

    public function ticket_info(Request $request, $id) {
        $id = $this->decode($id);
        $event = EH::GetEventData($id);
        $this->message = 'Not Found';
        $this->code = Response::HTTP_NOT_FOUND;
        if (!($event == null)) {
            if ($event['published']) {
                $this->success = true;
                $this->data = TH::GetTicket($id);
                $this->code = Response::HTTP_OK;
                $this->message = null;
            }
        }
        return $this->json();
    }

    public function ticket_create(Request $request) {
        $input = $request->all();
        $event_id = $this->decode($input['event']);
        $input['event'] = $event_id;
        $rules = [
            'event' => 'required|exists:tbl_venue,ven_id',
            'name' => 'required|max:100',
            'description' => 'max:200',
        ];
        $validation = Validator::make($input, $rules);
        if ($validation->fails()) {
            $this->message = $validation->errors();
        } else {
            $event = EH::GetEventData($event_id, true);
            $date_end = $event->chrono->date->end . ' 23:59:59';
            $rules = [
                'date_start' => "required|date|after_or_equal:today|before_or_equal:{$date_end}",
                'date_end' => "required|date|after_or_equal:date_start|before_or_equal:{$date_end}",
                'detail_name' => 'required|array',
                'detail_name.*' => 'required|max:100'
            ];
            $validation = Validator::make($input, $rules);
            if ($validation->fails()) {
                $this->message = $validation->errors();
            } else {
                $ticket_detail_length = count($input['detail_name']);
                $rules = [
                    'detail_description' => "required|array|min:{$ticket_detail_length}",
                    'detail_description.*' => 'max:200',
                    'detail_price' => "required|array|min:{$ticket_detail_length}",
                    'detail_price.*' => 'required|numeric',
                    'detail_quote' => "array|min:{$ticket_detail_length}",
                    'detail_quote.*' => 'required|numeric',
                ];
                $validation = Validator::make($input, $rules);
                if ($validation->fails()) {
                    $this->message = $validation->errors();
                } else {
                    \DB::beginTransaction();
                    try {
                        $event_ticket = new EventTicketModel([
                            'eti_eve_id' => $event_id,
                            'eti_name' => $request->name,
                            'eti_description' => ((blank($request->description)) ? null : $request->description),
                            'eti_date_start' => $request->date_start,
                            'eti_date_end' => $request->date_end,
                        ]);
                        $event_ticket->save();
                        for($i = 0; $i < $ticket_detail_length; $i++) {
                            $event_ticket_detail = new EventTicketDetailModel([
                                'etd_eti_id' => $event_ticket->eti_id,
                                'etd_name' => $request['detail_name'][$i],
                                'etd_description' => ((blank($request['detail_description'][$i])) ? null : $request['detail_description'][$i]),
                                'etd_price' => $request['detail_price'][$i],
                                'etd_quote' => $request['detail_quote'][$i],
                            ]);
                            $event_ticket_detail->save();
                            $redis_key = 'ticket:quote:' . $event_ticket_detail->etd_id;
                            RH::Set($redis_key, $request['detail_quote'][$i], RH::MONTH);
                            $event_ticket_detail_quote_history = new EventTicketQuoteHistoryModel([
                                'eth_etd_id' => $event_ticket_detail->etd_id,
                                'eth_type' => 'KREDIT',
                                'eth_note' => 'INITIAL',
                                'eth_value' => $request['detail_quote'][$i],
                                'eth_total' => $request['detail_quote'][$i],
                            ]);
                            $event_ticket_detail_quote_history->save();
                        }
                        TH::GetTicket($event_id, false, true);
                        \DB::commit();
                        $this->success = true;
                    } catch (\Exception $e) {
                        \DB::rollBack();
                        $this->message = $e->getMessage();
                        $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
                    }
                }
            }
        }
        return $this->json();
    }

    public function toggle(Request $request) {
        $rules = [
            'id' => 'required|exists:tbl_event,eve_id'
        ];
        $id = $request->id;
        $id = $this->decode($id);
        $validation = Validator::make(['id' => $id], $rules);
        if ($validation->fails()) {
            $this->message = $validation->errors();
        } else {
            $event = EH::GetEventData($id);
            if (!($event == null)) {
                $published = !$event['published'];
                \DB::beginTransaction();
                try {
                    EventModel::where('eve_id', $id)
                        ->update(['eve_published' => $published]);
                    \DB::commit();
                    $event['published'] = $published;
                    EH::UpdateEventData($event);
                    $this->success = true;
                    $this->data = ['published' => $published];
                } catch (\Exception $e) {
                    $this->message = $e->getMessage();
                    $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
                    \DB::rollBack();
                }
            } else {
                $this->message = 'Not Found';
                $this->code = Response::HTTP_NOT_FOUND;
            }
            return $this->json();
        }
    }

    public function event_info(Request $request, $id) {
        $id = $this->decode($id);
        $event = EH::GetEventData($id);
        $this->message = 'Not Found';
        $this->code = Response::HTTP_NOT_FOUND;
        if (!($event == null)) {
            if ($event['published']) {
                $this->success = true;
                $this->data = $event;
                $this->code = Response::HTTP_OK;
                $this->message = null;
            }
        }
        return $this->json();
    }

    public function create(Request $request) {
        $rules = [
            'venue' => 'required|exists:tbl_venue,ven_id',
            'type' => 'required|exists:tbl_event_type,ety_id',
            'organizer' => 'required|exists:tbl_organizer,org_id',
            'name' => 'required|max:150',
            'description' => 'max:10000',
            'date_start' => 'required|date|after_or_equal:today',
            'date_end' => 'required|date|after_or_equal:date_start',
            'schedule_date_start' => 'required|date|after_or_equal:date',
            'schedule_date_end' => 'required|date|after_or_equal:schedule_date_start|before_or_equal:date_end',
            'schedule_time_start' => 'required|date_format:H:i',
            'schedule_time_end' => 'required|date_format:H:i',
            'line_up' => 'array|distinct',
            'line_up.*' => 'exists:tbl_line_up,lin_id',
            'rule' => 'array|distinct',
            'rule.*' => 'exists:tbl_event_rule,eru_id',
            'tag' => 'array|distinct',
            'tag.*' => 'min:2|max:100',
        ];
        $validation = Validator::make($request->all(),$rules);
        if ($validation->fails()) {
            $this->message = $validation->errors();
        } else {
            $event = new EventModel([
                'eve_ety_id' => $request->type,
                'eve_ven_id' => $request->venue,
                'eve_org_id' => $request->organizer,
                'eve_name' => $request->name,
                'eve_description' => ((blank($request->description)) ? null : $request->description),
                'eve_date_start' => $request->date_start,
                'eve_date_end' => $request->date_end,
            ]);
            \DB::beginTransaction();
            try {
                $this->reload_date_time();
                $event->save();
                $event_tax = new EventTaxModel([
                   'etx_eve_id' => $event->eve_id,
                   'etx_tax' => env('DEFAULT_EVENT_TAX', 15),
                   'etx_description' => 'DEFAULT TAX',
                ]);
                $event_tax->save();
                $line_up_array = [];
                foreach ($request->line_up as $line_up) {
                    $line_up_array[] = [
                        'eli_eve_id' => $event->eve_id,
                        'eli_lin_id' => $line_up,
                    ];
                }
                if (count($line_up_array) > 0)
                    EventLineUpModel::insert($line_up_array);
                $rule_array = [];
                foreach ($request->rule as $rule) {
                    $rule_array[] = [
                        'ers_eve_id' => $event->eve_id,
                        'ers_eru_id' => $rule,
                    ];
                }
                if (count($rule_array) > 0)
                    EventRulesModel::insert($rule_array);
                $tag_array = [];
                foreach ($request->tag as $tag) {
                    $select = EventTagModel::select('eta_id')->where(\DB::raw('lower(eta_name)'), strtolower($tag))->first();
                    if ($select == null) {
                        $select = new EventTagModel([
                           'eta_name' => $tag,
                        ]);
                        $select->save();
                    }
                    $tag_array[] = [
                        'ets_eve_id' => $event->eve_id,
                        'ets_eta_id' => $select->eta_id,
                    ];
                }
                if (count($tag_array) > 0)
                    EventTagsModel::insert($tag_array);
                $event_schedule = new EventScheduleModel([
                    'esc_eve_id' => $event->eve_id,
                    'esc_date_start' => $request->schedule_date_start,
                    'esc_date_end' => $request->schedule_date_end,
                    'esc_time_start' => $request->schedule_time_start,
                    'esc_time_end' => $request->schedule_time_end,
                ]);
                $event_schedule->save();
                \DB::commit();
                $this->data = EH::GetEventData($event->eve_id);
                $this->success = true;
            } catch (\Exception $e) {
                $this->message = $e->getMessage();
                \DB::rollBack();
            }
        }
        return $this->json();
    }
}