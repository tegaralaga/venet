<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 15:58
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueTypeModel extends Model
{
    protected $fillable = [
        'vty_name',
    ];
    protected $primaryKey = 'vty_id';
    protected $table = 'tbl_venue_type';
}
