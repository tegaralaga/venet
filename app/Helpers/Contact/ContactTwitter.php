<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:55
 */

namespace App\Helpers\Contact;

class VCTwitter extends VenueContact
{
    public function __construct()
    {
        $this->setType('TWITTER');
    }
}