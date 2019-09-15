<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 22:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketModel extends Model
{
    protected $fillable = [
        'eti_eve_id',
        'eti_name',
        'eti_description',
        'eti_date_start',
        'eti_date_end',
    ];
    protected $primaryKey = 'eti_id';
    protected $table = 'tbl_event_ticket';
}