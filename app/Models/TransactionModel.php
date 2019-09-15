<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 01:33
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    protected $fillable = [
        'tra_cus_id',
        'tra_comment',
        'tra_status',
    ];
    protected $primaryKey = 'tra_id';
    protected $table = 'tbl_transaction';
}