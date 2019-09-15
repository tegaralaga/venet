<?php

use Illuminate\Database\Seeder;

class SeedOrganizer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_organizer;');
        \DB::statement('TRUNCATE tbl_organizer_contact;');
        $item = [
            'org_name' => null,
            'org_description' => null,
            'contacts' => [],
        ];
        $kaisar_kinanti = $item;
        $kaisar_kinanti['org_name'] = 'KaisarKinanti';
        $kaisar_kinanti_mobile_number = new \App\Helpers\Contact\ContactMobileNumber('+62 812-1216-8724', 'organizer');
        $kaisar_kinanti['contacts'][] = $kaisar_kinanti_mobile_number;
        $items = [];
        $items[] = $kaisar_kinanti;
        $this->insert($items);
    }

    public function insert($array) {
        foreach ($array as $item) {
            $this->processOrganizer($item);
        }
    }

    public function processOrganizer($item, \App\Models\OrganizerModel $parent = null) {
        $data = $item;
        unset($data['contacts']);
        $organizer = new \App\Models\OrganizerModel($data);
        if ($organizer->save()) {
            $this->processOrganizerContact($item['contacts'], $organizer);
        }
    }

    public function processOrganizerContact($organizer_contacts, \App\Models\OrganizerModel $parent = null) {
        foreach ($organizer_contacts as $contact) {
            $contact->setParent($parent->org_id);
            if(!$contact->save()) {
                $this->command->error('FAILED SAVING CONTACT');
            }
        }
    }
}
