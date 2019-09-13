<?php

use Illuminate\Database\Seeder;

class SeedVenue extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_venue;');
        \DB::statement('TRUNCATE tbl_venue_contact;');
        $data = [];
        $item = [
            'ven_parent_id' => 0,
            'ven_vty_id' => 0,
            'ven_location_type' => 'SEMI',
            'ven_kel_id' => 0,
            'ven_capacity' => 0,
            'ven_address' => null,
            'ven_name' => null,
            'ven_description' => null,
            'contacts' => [],
            'children' => [],
        ];
        $mason_pine = $item;
        $mason_pine['ven_vty_id'] = 'Hotel';
        $mason_pine['ven_kel_id'] = 5576;
        $mason_pine['ven_address'] = 'Jalan Raya Parahyangan KM. 1.8 Kota Baru Parahyangan, Bandung 40714, Indonesia';
        $mason_pine['ven_name'] = 'Mason Pine Hotel';
        $mason_pine_phone_number = new \App\Helpers\VenueContact\VCPhoneNumber();
        $mason_pine_phone_number->setValue('(+62 22) 680 3778');
        $mason_pine_mobile_number = new \App\Helpers\VenueContact\VCMobileNumber();
        $mason_pine_mobile_number->setValue('(+62) 852 2229 2229');
        $mason_pine_mobile_number->setDescription('Available for WhatsApp and Telegram');
        $mason_pine_fax_number = new \App\Helpers\VenueContact\VCFaxNumber();
        $mason_pine_fax_number->setValue('(+62 22) 680 3779');
        $mason_pine_email = new \App\Helpers\VenueContact\VCEmail();
        $mason_pine_email->setValue('reservation@masonpinehotel.com');
        $mason_pine_twitter = new \App\Helpers\VenueContact\VCTwitter();
        $mason_pine_twitter->setValue('@masonpine');
        $mason_pine_facebook = new \App\Helpers\VenueContact\VCFacebook();
        $mason_pine_facebook->setValue('https://www.facebook.com/pages/Mason-Pine-Hotel/82213385138');
        $mason_pine_instagram = new \App\Helpers\VenueContact\VCInstagram();
        $mason_pine_instagram->setValue('@masonpinehotel');
        $mason_pine_website = new \App\Helpers\VenueContact\VCWebsite();
        $mason_pine_website->setValue('http://masonpinehotel.com/');
        $mason_pine_tripadvisor = new \App\Helpers\VenueContact\VCWebsite();
        $mason_pine_tripadvisor->setValue('http://www.tripadvisor.co.id/Hotel_Review-g297704-d1389881-Reviews-Mason_Pine_Hotel-Bandung_West_Java_Java.html');
        $mason_pine_tripadvisor->setDescription('Trip Advisor');
        $mason_pine['contacts'][] = $mason_pine_phone_number;
        $mason_pine['contacts'][] = $mason_pine_mobile_number;
        $mason_pine['contacts'][] = $mason_pine_fax_number;
        $mason_pine['contacts'][] = $mason_pine_twitter;
        $mason_pine['contacts'][] = $mason_pine_facebook;
        $mason_pine['contacts'][] = $mason_pine_instagram;
        $mason_pine['contacts'][] = $mason_pine_website;
        $mason_pine['contacts'][] = $mason_pine_tripadvisor;
        $bale_bale_lounge = $item;
        $bale_bale_lounge['ven_vty_id'] = 'Restoran';
        $bale_bale_lounge['ven_name'] = 'Bale Bale Lounge';
        $bale_bale_lounge['contacts'][] = $mason_pine_phone_number;
        $chips_water_world = $item;
        $chips_water_world['ven_vty_id'] = 'Kolam Renang Anak';
        $chips_water_world['ven_name'] = "Chip's Water World";
        $chips_water_world['ven_location_type'] = "OUTDOOR";
        $chips_water_world['ven_description'] = "Kolam Renang Anak";
        $chips_water_world['contacts'][] = $mason_pine_phone_number;
        $chups_play_club = $item;
        $chups_play_club['ven_vty_id'] = 'Tempat Bermain';
        $chups_play_club['ven_name'] = "Chup's Play Club";
        $chups_play_club['ven_location_type'] = "INDOOR";
        $chups_play_club['contacts'][] = $mason_pine_phone_number;
        $mason_pine['children'][] = $bale_bale_lounge;
        $mason_pine['children'][] = $chips_water_world;
        $mason_pine['children'][] = $chups_play_club;
        $items = [];
        $items[] = $mason_pine;
        $this->insert($items);
    }

    public function insert($array) {
        foreach ($array as $item) {
            $this->processVenue($item);
        }
    }

    public function processVenue($item, \App\Models\VenueModel $parent = null) {
        if (!($parent == null)) {
            $item['ven_parent_id'] = $parent->ven_id;
            if ($item['ven_kel_id'] == 0) {
                $item['ven_kel_id'] = $parent->ven_kel_id;
            }
            if ($item['ven_address'] == null) {
                $item['ven_address'] = $parent->ven_address;
            }
        }
        $venue_type = \App\Models\VenueTypeModel::select('vty_id')->where('vty_name', $item['ven_vty_id'])->first();
        if (!($venue_type == null)) {
            $kelurahan = \App\Models\KelurahanModel::select('kel_id')->where('kel_id', $item['ven_kel_id'])->count();
            if ($kelurahan == 1) {
                $venue = new \App\Models\VenueModel();
                $venue->ven_parent_id = $item['ven_parent_id'];
                $venue->ven_vty_id = $venue_type->vty_id;
                $venue->ven_location_type = $item['ven_location_type'];
                $venue->ven_kel_id = $item['ven_kel_id'];
                $venue->ven_capacity = $item['ven_capacity'];
                $venue->ven_address = $item['ven_address'];
                $venue->ven_name = $item['ven_name'];
                $venue->ven_description = $item['ven_description'];
                if ($venue->save()) {
                    foreach ($item['children'] as $children) {
                        $this->processVenue($children, $venue);
                    }
                    $this->processVenueContact($item['contacts'], $venue);
                }
            }
        }
    }

    public function processVenueContact($venue_contacts, \App\Models\VenueModel $parent) {
        foreach ($venue_contacts as $contact) {
            $contact->setVenue($parent->ven_id);
            $contact->save();
        }
    }
}
