<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUpContactModel extends Model
{
    protected $fillable = [
        'lco_lin_id',
        'lco_type',
        'lco_value',
        'lco_description',
    ];
    protected $primaryKey = 'lco_id';
    protected $table = 'tbl_line_up_contact';
}