<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:53
 */

namespace App\Helpers\VenueContact;

class VCPhoneNumber extends VenueContact
{
    public function __construct()
    {
        $this->setType('PHONE_NUMBER');
    }
}