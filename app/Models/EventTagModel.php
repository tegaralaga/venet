<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 17:44
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTagModel extends Model
{
    protected $fillable = [
        'eta_name',
    ];
    protected $primaryKey = 'eta_id';
    protected $table = 'tbl_event_tag';
}