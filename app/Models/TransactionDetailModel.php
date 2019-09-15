<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 01:34
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetailModel extends Model
{
    protected $fillable = [
        'tde_tra_id',
        'tde_etd_id',
        'tde_price',
        'tde_tax',
        'tde_quantity',
        'tde_total',
    ];
    protected $primaryKey = 'tde_id';
    protected $table = 'tbl_transaction_detail';
    public $timestamps = false;

}