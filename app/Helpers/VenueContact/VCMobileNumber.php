<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:54
 */

namespace App\Helpers\VenueContact;

class VCMobileNumber extends VenueContact
{
    public function __construct()
    {
        $this->setType('MOBILE_NUMBER');
    }
}