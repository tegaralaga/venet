<?php

use Illuminate\Database\Seeder;

class SeedNegara extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn("TRUNCATE tbl_negara");
        \DB::statement('TRUNCATE tbl_negara;');
        $this->command->warn("tbl_negara TRUNCATED");
        $this->command->info("NEGARA [INDONESIA] ADDED TO tbl_negara");
        $this->command->warn("INSERTING NEGARA");
        \DB::insert('INSERT INTO tbl_negara (neg_id, neg_name) VALUES (?, ?)', [1, 'INDONESIA']);
        $this->command->warn("NEGARA INSERTED");
    }
}
