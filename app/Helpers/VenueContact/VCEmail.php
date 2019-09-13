<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 17:01
 */

namespace App\Helpers\VenueContact;

class VCEmail extends VenueContact
{
    public function __construct()
    {
        $this->setType('EMAIL');
    }
}