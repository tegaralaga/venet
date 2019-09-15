<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-13
 * Time: 16:56
 */

namespace App\Helpers\Contact;

class ContactFacebook extends Contact
{
    public function __construct($value = null, $model = 'venue', $description = null)
    {
        $this->setValue($value);
        $this->setModel($model);
        $this->setDescription($description);
        $this->setType('FACEBOOK');
    }
}