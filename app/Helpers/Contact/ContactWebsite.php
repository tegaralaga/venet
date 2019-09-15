<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:58
 */

namespace App\Helpers\Contact;

class ContactWebsite extends Contact
{
    public function __construct($value = null, $model = 'venue', $description = null)
    {
        $this->setValue($value);
        $this->setModel($model);
        $this->setDescription($description);
        $this->setType('WEBSITE');
    }
}