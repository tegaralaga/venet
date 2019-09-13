<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:42
 */

namespace App\Helpers\VenueContact;

abstract class VenueContact {
    private $venue = 0;
    private $type = "PHONE_NUMBER";
    private $value = null;
    private $description = null;
    private $venue_contact = null;

    public function setVenue($venue) {
        $this->venue = $venue;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getVenueContact() {
        if ($this->venue_contact == null) {
            $this->venue_contact = new \App\Models\VenueContactModel();
        }
        $this->venue_contact->vco_ven_id = $this->venue;
        $this->venue_contact->vco_type = $this->type;
        $this->venue_contact->vco_value = $this->value;
        $this->venue_contact->vco_description = $this->description;
        return $this->venue_contact;
    }

    public function save() {
        $this->getVenueContact();
        return $this->venue_contact->save();
    }

}