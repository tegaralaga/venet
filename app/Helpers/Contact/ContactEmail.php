<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 17:01
 */

namespace App\Helpers\Contact;

class ContactEmail extends Contact
{
    public function __construct($value = null, $model = 'venue', $description = null)
    {
        $this->setValue($value);
        $this->setModel($model);
        $this->setDescription($description);
        $this->setType('EMAIL');
    }
}