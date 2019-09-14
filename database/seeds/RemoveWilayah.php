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
        $this->command->warn('DROPPING kel_kode FROM tbl_kelurahan');
        \DB::statement('ALTER TABLE tbl_kelurahan DROP COLUMN kel_kode');
        $this->command->info('kel_kode DROPPED FROM tbl_kelurahan');
        $this->command->warn('DROPPING kec_kode FROM tbl_kecamatan');
        \DB::statement('ALTER TABLE tbl_kecamatan DROP COLUMN kec_kode');
        $this->command->info('kec_kode DROPPED FROM tbl_kecamatan');
        $this->command->warn('DROPPING kot_kode FROM tbl_kota');
        \DB::statement('ALTER TABLE tbl_kota DROP COLUMN kot_kode');
        $this->command->info('kot_kode DROPPED FROM tbl_kota');
        $this->command->warn('DROPPING pro_kode FROM tbl_provinsi');
        \DB::statement('ALTER TABLE tbl_provinsi DROP COLUMN pro_kode');
        $this->command->info('pro_kode DROPPED FROM tbl_provinsi');
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
