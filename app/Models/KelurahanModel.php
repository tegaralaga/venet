<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 17:58
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelurahanModel extends Model
{
    protected $fillable = [
        'kel_kec_id',
        'kel_name',
        'kel_kode',
    ];
    protected $primaryKey = 'kel_id';
    protected $table = 'tbl_kelurahan';
}