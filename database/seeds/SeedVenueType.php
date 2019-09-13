<?php

use Illuminate\Database\Seeder;

class SeedVenueType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_venue_type;');
        $venue_types = [
            'Ballroom',
            'Hotel',
            'Lapangan Parkir',
            'Lapangan Olahraga',
            'Kedai Kopi',
            'Kafe',
            'Bar/Pub/Klub',
            'Kolam Renang',
            'Kolam Renang Umum',
            'Kolam Renang Anak',
            'Kolam Renang Dewasa',
            'Restoran',
            'Bistro',
            'Gedung Serba Guna',
            'Pusat Kebugaran',
            'Gelanggang Olahraga',
            'Stadion',
            'Bioskop',
            'Teater',
            'Tempat Bermain'
        ];
        foreach ($venue_types as $type) {
            $venue_type = new \App\Models\VenueTypeModel();
            $venue_type->vty_name = $type;
            $venue_type->save();
        }

    }
}
