<?php

use Illuminate\Database\Seeder;

class EventTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_event_tag;');
        $event_tags = [
            'Galau',
            'Sobat Ambyar'
        ];
        foreach ($event_tags as $tag) {
            $event_tag = new \App\Models\EventTagModel();
            $event_tag->eta_name = $tag;
            $event_tag->save();
        }
    }
}
