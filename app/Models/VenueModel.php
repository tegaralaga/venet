<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:15
 */

namespace App\Models;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

/**
 * @property \Grimzy\LaravelMysqlSpatial\Types\Point   $ven_coordinate
 */

use Illuminate\Database\Eloquent\Model;

class VenueModel extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'ven_parent_id',
        'ven_vty_id',
        'ven_location_type',
        'ven_kel_id',
        'ven_capacity',
        'ven_address',
        'ven_name',
        'ven_description',
        'ven_coordinate',
    ];
    protected $spatialFields = [
        'ven_coordinate',
    ];
    protected $primaryKey = 'ven_id';
    protected $table = 'tbl_venue';
}
