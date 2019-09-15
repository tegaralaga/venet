<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 18:58
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLineUpModel extends Model
{
    protected $fillable = [
        'eli_eve_id',
        'eli_lin_id',
    ];
    protected $primaryKey = 'eli_id';
    protected $table = 'tbl_event_line_up';
    public $timestamps = false;
}