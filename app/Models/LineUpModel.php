<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUpModel extends Model
{
    protected $fillable = [
        'lin_lty_id',
        'lin_name',
        'lin_description'
    ];
    protected $primaryKey = 'lin_id';
    protected $table = 'tbl_line_up';
}