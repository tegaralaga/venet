<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 22:59
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketQuoteHistoryModel extends Model
{
    protected $fillable = [
        'eth_etd_id',
        'eth_type',
        'eth_note',
        'eth_value',
        'eth_total',
        'eth_comment',
    ];
    protected $primaryKey = 'eth_id';
    protected $table = 'tbl_event_ticket_quote_history';
    public $timestamps = false;
}