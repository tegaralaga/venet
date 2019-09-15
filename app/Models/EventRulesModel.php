<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 18:59
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRulesModel extends Model
{
    protected $fillable = [
        'ers_eve_id',
        'ers_eru_id',
    ];
    protected $primaryKey = 'ers_id';
    protected $table = 'tbl_event_rules';
    public $timestamps = false;
}