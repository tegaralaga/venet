<?php

use Illuminate\Database\Seeder;

class SeedKota extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn("TRUNCATE tbl_kota");
        \DB::statement('TRUNCATE tbl_kota;');
        $this->command->info("tbl_kota TRUNCATED");
        $this->command->warn("SELECTING PROVINSI");
        $daftar_provinsi = \DB::select('SELECT pro_id, pro_name, pro_kode FROM tbl_provinsi;');
        $this->command->info("PROVINSI SELECTED");
        if (count($daftar_provinsi) > 0) {
            foreach ($daftar_provinsi as $provinsi) {
                $daftar_kota = \DB::select('SELECT * FROM a_id_territory WHERE LEVEL = ? AND MST_KODE_WILAYAH = ?', [2, $provinsi->pro_kode]);
                $values = [];
                $data = [];
                if (count($daftar_kota) > 0) {
                    foreach ($daftar_kota as $kota) {
                        $values[] = '(?, ?, ?)';
                        $data[] = $provinsi->pro_id;
                        $name = $kota->NAMA;
                        if (\Illuminate\Support\Str::startsWith($name, 'KOTA')) {
                            $name = substr($name, 5, (strlen($name)-5));
                        } else if (\Illuminate\Support\Str::startsWith($name, 'KAB.')) {
                            $name = str_replace('KAB.', 'KABUPATEN', $name);
                        }
                        $data[] = $name;
                        $data[] = $kota->KODE_WILAYAH;
                        $this->command->info("ADDED KOTA/KABUPATEN [{$name}] TO PROVINSI [{$provinsi->pro_name}]");
                    }
                    $this->command->warn("INSERTING KOTA/KABUPATEN TO PROVINSI [{$provinsi->pro_name}]");
                    $values = implode(', ', $values);
                    $sql = "INSERT INTO tbl_kota (kot_pro_id, kot_name, kot_kode) VALUES {$values};";
                    \DB::insert($sql, $data);
                    $this->command->warn("KOTA/KABUPATEN INSERTED TO PROVINSI [{$provinsi->pro_name}]");
                }
            }
        }
    }
}
