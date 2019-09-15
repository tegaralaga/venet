<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 03:00
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetailTicketModel extends Model
{
    protected $fillable = [
        'tdt_tde_id',
        'tdt_checked_in',
        'tdt_check_in_datetime',
    ];
    protected $primaryKey = 'tdt_id';
    protected $table = 'tbl_transaction_detail_ticket';
    public $timestamps = false;
}