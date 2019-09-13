<?php

use Illuminate\Database\Seeder;

class SeedKecamatan extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn("TRUNCATE tbl_kecamatan");
        \DB::statement('TRUNCATE tbl_kecamatan;');
        $this->command->info("tbl_kecamatan TRUNCATED");
        $this->command->warn("SELECTING KOTA");
        $daftar_kota = \DB::select('SELECT kot_id, kot_name, kot_kode FROM tbl_kota;');
        $this->command->warn("KOTA SELECTED");
        if (count($daftar_kota) > 0) {
            foreach ($daftar_kota as $kota) {
                $daftar_kecamatan = \DB::select('SELECT * FROM a_id_territory WHERE LEVEL = ? AND MST_KODE_WILAYAH = ?', [3, $kota->kot_kode]);
                $values = [];
                $data = [];
                if (count($daftar_kecamatan) > 0) {
                    foreach ($daftar_kecamatan as $kecamatan) {
                        $values[] = '(?, ?, ?)';
                        $data[] = $kota->kot_id;
                        $name = $kecamatan->NAMA;
                        if (\Illuminate\Support\Str::startsWith($name, 'KEC.')) {
                            $name = str_replace('KEC. ', '', $name);
                        }
                        $data[] = $name;
                        $data[] = $kecamatan->KODE_WILAYAH;
                        $this->command->info("ADDED KECAMATAN [{$name}] TO KOTA [{$kota->kot_name}]");
                    }
                    $this->command->warn("INSERTING KECAMATAN TO KOTA [{$kota->kot_name}]");
                    $values = implode(', ', $values);
                    $sql = "INSERT INTO tbl_kecamatan (kec_kot_id, kec_name, kec_kode) VALUES {$values};";
                    \DB::insert($sql, $data);
                    $this->command->info("KECAMATAN INSERTED TO KOTA [{$kota->kot_name}]");
                }
            }
        }
    }
}
