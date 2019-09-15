<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:57
 */

namespace App\Helpers\Contact;

class VCInstagram extends VenueContact
{
    public function __construct()
    {
        $this->setType('INSTAGRAM');
    }
}