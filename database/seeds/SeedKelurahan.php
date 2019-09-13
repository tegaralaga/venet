<?php

use Illuminate\Database\Seeder;

class SeedKelurahan extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn("TRUNCATE tbl_kelurahan");
        \DB::statement('TRUNCATE tbl_kelurahan;');
        $this->command->info("tbl_kelurahan TRUNCATED");
        $this->command->warn("SELECTING KECAMATAN");
        $daftar_kecamatan = \DB::select('SELECT kec_id, kec_name, kec_kode FROM tbl_kecamatan;');
        $this->command->warn("KECAMATAN SELECTED");
        if (count($daftar_kecamatan) > 0) {
            foreach ($daftar_kecamatan as $kecamatan) {
                $daftar_kelurahan = \DB::select('SELECT * FROM a_id_territory WHERE LEVEL = ? AND MST_KODE_WILAYAH = ?', [4, $kecamatan->kec_kode]);
                $values = [];
                $data = [];
                if (count($daftar_kelurahan) > 0) {
                    foreach ($daftar_kelurahan as $kelurahan) {
                        $values[] = '(?, ?, ?)';
                        $data[] = $kecamatan->kec_id;
                        $data[] = $kelurahan->NAMA;
                        $data[] = $kelurahan->KODE_WILAYAH;
                        $this->command->info("ADDED KELURAHAN [{$kelurahan->NAMA}] TO KECAMATAN [{$kecamatan->kec_name}]");
                    }
                    $this->command->warn("INSERTING KELURAHAN TO KECAMATAN [{$kecamatan->kec_name}]");
                    $values = implode(', ', $values);
                    $sql = "INSERT INTO tbl_kelurahan (kel_kec_id, kel_name, kel_kode) VALUES {$values};";
                    \DB::insert($sql, $data);
                    $this->command->info("KELURAHAN INSERTED TO KECAMATAN [{$kecamatan->kec_name}]");
                }
            }
        }
    }
}
