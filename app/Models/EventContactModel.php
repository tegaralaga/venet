<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 19:35
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventContactModel extends Model
{
    protected $fillable = [
        'eco_lin_id',
        'eco_type',
        'eco_value',
        'eco_description',
    ];
    protected $primaryKey = 'eco_id';
    protected $table = 'tbl_event_contact';
}