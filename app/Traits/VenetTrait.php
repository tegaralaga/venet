<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 22:42
 */
namespace App\Traits;

use App\Helpers\TimeHelper;
use Carbon\Carbon;
use LaravelHashids\Facades\Hashids;

trait VenetTrait
{

    public $carbon = null;
    public $carbon_utc = null;
    public $now = null;
    public $now_utc = null;
    public $timestamp = null;
    public $timestamp_utc = null;
    public $date = null;
    public $date_utc = null;
    public $time = null;
    public $time_utc = null;
    public $success = false;
    public $message = null;
    public $data = null;
    public $code = \Illuminate\Http\Response::HTTP_OK;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function json() : \Illuminate\Http\JsonResponse
    {
        $result = array();
        $result['success'] = $this->success;
        $result['message'] = $this->message;
        if (is_array($this->data) || is_object($this->data))
            $result['data'] = $this->data;
        $result['elapsed'] = TimeHelper::server_elapsed_time();
        return response()->json($result, $this->code, [], JSON_PRETTY_PRINT);
    }

    public function reload_date_time()
    {
        $this->carbon = Carbon::now('Asia/Jakarta');
        $this->carbon_utc = Carbon::now('UTC');
        $this->now = $this->carbon->format('Y-m-d H:i:s');
        $this->now_utc = $this->carbon_utc->format('Y-m-d H:i:s');
        $this->timestamp = $this->carbon->getTimestamp();
        $this->timestamp_utc = $this->carbon_utc->getTimestamp();
        $this->date = $this->carbon->format('Y-m-d');
        $this->date_utc = $this->carbon_utc->format('Y-m-d');
        $this->time = $this->carbon->format('H:i:s');
        $this->time_utc = $this->carbon_utc->format('H:i:s');
    }

    public function encode($value) {
        return Hashids::encode($value);
    }

    public function decode($value) {
        $decoded = Hashids::decode($value);
        if (count($decoded) == 0)
            return 0;
        return $decoded[0];
    }

}