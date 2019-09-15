<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 18:59
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTagsModel extends Model
{
    protected $fillable = [
        'ets_eve_id',
        'ets_eta_id',
    ];
    protected $primaryKey = 'ets_id';
    protected $table = 'tbl_event_tags';
    public $timestamps = false;
}