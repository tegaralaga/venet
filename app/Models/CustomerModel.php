<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 01:33
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    protected $fillable = [
        'cus_kel_id',
        'cus_name',
        'cus_gender',
        'cus_relationship',
        'cus_birthday',
        'cus_address',
    ];
    protected $primaryKey = 'cus_id';
    protected $table = 'tbl_customer';
}