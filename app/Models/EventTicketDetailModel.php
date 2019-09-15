<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 22:57
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketDetailModel extends Model
{
    protected $fillable = [
        'etd_eti_id',
        'etd_name',
        'etd_description',
        'etd_price',
        'etd_quote',
    ];
    protected $primaryKey = 'etd_id';
    protected $table = 'tbl_event_ticket_detail';
}