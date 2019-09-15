<?php

use Illuminate\Database\Seeder;

class VenueReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SeedVenueType::class,
            SeedVenue::class,
        ]);
    }
}
