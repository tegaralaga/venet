<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 16:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTypeModel extends Model
{
    protected $fillable = [
        'ety_name',
    ];
    protected $primaryKey = 'ety_id';
    protected $table = 'tbl_event_type';
}