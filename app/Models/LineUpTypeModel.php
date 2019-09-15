<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUpTypeModel extends Model
{
    protected $fillable = [
        'lty_name',
    ];
    protected $primaryKey = 'lty_id';
    protected $table = 'tbl_line_up_type';
}