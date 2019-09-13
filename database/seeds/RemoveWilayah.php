<?php

use Illuminate\Database\Seeder;

class RemoveWilayah extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn('DROPPING TABLE a_id_territory');
        \DB::statement('DROP TABLE IF EXISTS a_id_territory;');
        $this->command->info('TABLE a_id_territory DROPPED');
        $file = getcwd() . env('IMPORT_WILAYAH_FILE', '/wilayah/data-semua-wilayah-indonesia-000000-20190428131634.sql');
        if (file_exists($file)) {
            $this->command->warn("DELETING FILE [{$file}]");
            unlink($file);
            $this->command->info("FILE [{$file}] DELETED");
            $this->command->warn('DELETING DIRECTORY [' . dirname($file) . ']');
            rmdir(dirname($file));
            $this->command->info('DIRECTORY [' . dirname($file) . '] DELETED');
        }
    }
}
