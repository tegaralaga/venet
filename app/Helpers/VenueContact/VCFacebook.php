<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:56
 */

namespace App\Helpers\VenueContact;

class VCFacebook extends VenueContact
{
    public function __construct()
    {
        $this->setType('FACEBOOK');
    }
}