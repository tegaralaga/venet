<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 23:49
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTaxModel extends Model
{
    protected $fillable = [
        'etx_event_id',
        'etx_tax',
        'etx_description'
    ];
    protected $primaryKey = 'etx_id';
    protected $table = 'tbl_event_tax';
    public $timestamps = false;
}