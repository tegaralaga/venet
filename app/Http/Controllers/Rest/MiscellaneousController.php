<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 22:44
 */

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\VenetTrait;

class MiscellaneousController extends Controller
{

    use VenetTrait;

    public function index(Request $request) {
        $this->success = true;
        $this->message = env('APP_NAME', null);
        return $this->json();
    }

}