<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:42
 */

namespace App\Helpers\Contact;

use App\Models\CustomerContactModel;
use App\Models\EventContactModel;
use App\Models\LineUpContactModel;
use App\Models\OrganizerContactModel;

abstract class Contact {
    private $parent = 0;
    private $type = "PHONE_NUMBER";
    private $value = null;
    private $description = null;
    private $contact = null;
    private $model = 'venue';

    public function setParent($venue) {
        $this->parent = $venue;
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

    public function setModel($model = 'venue') {
        $this->model = $model;
    }

    public function GetContact() {
        switch ($this->model) {
            case 'venue':
                if ($this->contact == null) {
                    $this->contact = new \App\Models\VenueContactModel([
                        'vco_ven_id' => $this->parent,
                        'vco_type' => $this->type,
                        'vco_value' => $this->value,
                        'vco_description' => $this->description,
                    ]);
                }
                break;
            case 'line_up':
                if ($this->contact == null) {
                    $this->contact = new LineUpContactModel([
                        'lco_lin_id' => $this->parent,
                        'lco_type' => $this->type,
                        'lco_value' => $this->value,
                        'lco_description' => $this->description,
                    ]);
                }
                break;
            case 'organizer':
                if ($this->contact == null) {
                    $this->contact = new OrganizerContactModel([
                        'oco_org_id' => $this->parent,
                        'oco_type' => $this->type,
                        'oco_value' => $this->value,
                        'oco_description' => $this->description,
                    ]);
                }
                break;
            case 'event':
                if ($this->contact == null) {
                    $this->contact = new EventContactModel([
                        'eco_org_id' => $this->parent,
                        'eco_type' => $this->type,
                        'eco_value' => $this->value,
                        'eco_description' => $this->description,
                    ]);
                }
                break;
            case 'customer':
                if ($this->contact == null) {
                    $this->contact = new CustomerContactModel([
                        'cco_cus_id' => $this->parent,
                        'cco_type' => $this->type,
                        'cco_value' => $this->value,
                        'cco_description' => $this->description,
                    ]);
                }
                break;
        }
        return $this->contact;
    }

    public function save() {
        $this->GetContact();
        return $this->contact->save();
    }

}