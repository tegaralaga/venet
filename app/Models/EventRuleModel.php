<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 17:06
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRuleModel extends Model
{
    protected $fillable = [
        'eru_text_id',
        'eru_text_en',
        'eru_general'
    ];
    protected $primaryKey = 'eru_id';
    protected $table = 'tbl_event_rule';
}