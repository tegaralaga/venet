<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_customer;');
        \DB::statement('TRUNCATE tbl_customer_contact;');
        $item = [
            'cus_kel_id' => 0,
            'cus_name' => null,
            'cus_gender' => 'NONE',
            'cus_relationship' => 'NONE',
            'cus_birthday' => null,
            'cus_address' => null,
            'contacts' => [],
        ];
        $bias_tegaralaga = $item;
        $bias_tegaralaga['cus_kel_id'] = 5580;
        $bias_tegaralaga['cus_name'] = 'Bias Tegaralaga Sugestiawan';
        $bias_tegaralaga['cus_gender'] = 'MALE';
        $bias_tegaralaga['cus_relationship'] = 'MARRIED';
        $bias_tegaralaga['cus_birthday'] = '1990-01-17';
        $bias_tegaralaga['cus_address'] = 'Bukit Berlian Blok C No 106 RT 04/25 40553';
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactMobileNumber('+62 812-6069-5203', 'customer');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactTwitter('@tegaralaga', 'customer');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactInstagram('@tegaralaga', 'customer');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactEmail('tegaralaga@live.com', 'customer');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactWebsite('https://stackoverflow.com/users/story/696259', 'customer', 'StackOverflow Story');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactWebsite('https://linkedin.com/in/tegaralaga', 'customer', 'LinkedIn');
        $bias_tegaralaga['contacts'][] = new \App\Helpers\Contact\ContactWebsite('https://github.com/tegaralaga', 'customer', 'Github');
        $anonymous = $item;
        $anonymous['cus_name'] = 'Anonymous';
        $items = [];
        $items[] = $bias_tegaralaga;
        $items[] = $anonymous;
        $this->insert($items);
    }

    public function insert($array) {
        foreach ($array as $item) {
            $this->processCustomer($item);
        }
    }

    public function processCustomer($item) {
        $data = $item;
        unset($data['contacts']);
        $customer = new \App\Models\CustomerModel($data);
        if ($customer->save()) {
            $this->processLineUpContact($item['contacts'], $customer);
        }
    }

    public function processLineUpContact($customer_contacts, \App\Models\CustomerModel $parent = null) {
        foreach ($customer_contacts as $contact) {
            $contact->setParent($parent->cus_id);
            if(!$contact->save()) {
                $this->command->error('FAILED SAVING CONTACT');
            }
        }
    }
}
