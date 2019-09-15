<?php

use Illuminate\Database\Seeder;

class SeedLineUpType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_line_up_type;');
        $venue_types = [
            'Individu',
            'Band',
            'Penyanyi',
            'Motivator',
            'Influencer',
            'Software Engineer',
            'Nonprofit Organization'
        ];
        foreach ($venue_types as $type) {
            $line_up_type = new \App\Models\LineUpTypeModel();
            $line_up_type->lty_name = $type;
            $line_up_type->save();
        }
    }
}
