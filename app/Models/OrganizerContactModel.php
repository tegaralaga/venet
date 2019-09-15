<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:56
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerContactModel extends Model
{
    protected $fillable = [
        'oco_org_id',
        'oco_type',
        'oco_value',
        'oco_description',
    ];
    protected $primaryKey = 'oco_id';
    protected $table = 'tbl_organizer_contact';
}