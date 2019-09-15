<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-16
 * Time: 01:33
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContactModel extends Model
{
    protected $fillable = [
        'cco_cus_id',
        'cco_type',
        'cco_value',
        'cco_description',
    ];
    protected $primaryKey = 'cco_id';
    protected $table = 'tbl_customer_contact';
}