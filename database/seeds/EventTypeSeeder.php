<?php

use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_event_type;');
        $event_type = [
            'Concert',
            'Exhibition',
            'Art',
            'Seminar',
            'Conference',
            'Trade Show',
            'Summit',
            'Wedding',
            'Sport',
            'Festival',
            'Workshop',
            'Theater & Drama Musical',
            'Attraction',
            'Bazaar',
            'Nightlife',
            'Performance',
            'Travel',
            'Adventure',
        ];
        foreach ($event_type as $type) {
            $event_type = new \App\Models\EventTypeModel();
            $event_type->ety_name = $type;
            $event_type->save();
        }
    }
}
