<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-15
 * Time: 15:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineUpMemberModel extends Model
{
    protected $fillable = [
        'lme_lin_id',
        'lem_belong_to',
    ];
    protected $primaryKey = 'lme_id';
    protected $table = 'tbl_line_up_member';
    public $timestamps = false;
}