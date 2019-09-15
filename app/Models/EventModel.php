<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 18:56
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventModel extends Model
{
    protected $fillable = [
        'eve_ety_id',
        'eve_ven_id',
        'eve_org_id',
        'eve_name',
        'eve_description',
        'eve_date_start',
        'eve_date_end',
        'eve_published',
    ];
    protected $primaryKey = 'eve_id';
    protected $table = 'tbl_event';
}