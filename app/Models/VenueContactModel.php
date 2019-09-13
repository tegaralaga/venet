<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:32
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueContactModel extends Model
{
    protected $fillable = [
        'vco_ven_id',
        'vco_type',
        'vco_value',
        'vco_description',
    ];
    protected $primaryKey = 'vco_id';
    protected $table = 'tbl_venue_contact';
}
