<?php

use Illuminate\Database\Seeder;

class EventReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SeedLineUpType::class,
            SeedLineUp::class,
            SeedOrganizer::class,
            EventTypeSeeder::class,
            EventRuleSeeder::class,
            EventTagSeeder::class,
        ]);
    }
}
