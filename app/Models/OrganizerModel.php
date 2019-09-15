<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerModel extends Model
{
    protected $fillable = [
        'org_name',
        'org_description'
    ];
    protected $primaryKey = 'org_id';
    protected $table = 'tbl_organizer';
}