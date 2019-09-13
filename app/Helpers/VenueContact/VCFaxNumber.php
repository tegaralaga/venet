<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:55
 */

namespace App\Helpers\VenueContact;

class VCFaxNumber extends VenueContact
{
    public function __construct()
    {
        $this->setType("FAX_NUMBER");
    }
}