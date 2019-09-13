<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:58
 */

namespace App\Helpers\VenueContact;

class VCWebsite extends VenueContact
{
    public function __construct()
    {
        $this->setType('WEBSITE');
    }
}