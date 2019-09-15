<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 18:59
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventScheduleModel extends Model
{
    protected $fillable = [
        'esc_eve_id',
        'esc_date_start',
        'esc_date_end',
        'esc_time_start',
        'esc_time_end',
    ];
    protected $primaryKey = 'esc_id';
    protected $table = 'tbl_event_schedule';
}