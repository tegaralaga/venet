<?php

use Illuminate\Database\Seeder;

class SeedProvinsi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->warn("TRUNCATE tbl_provinsi");
        \DB::statement('TRUNCATE tbl_provinsi;');
        $this->command->info("tbl_provinsi TRUNCATED");
        $this->command->warn("SELECT FROM tbl_provinsi");
        $daftar_provinsi = \DB::select('SELECT * FROM a_id_territory WHERE LEVEL = ?', [1]);
        $this->command->warn("tbl_provinsi SELECTED");
        if (count($daftar_provinsi) > 0) {
            $values = [];
            $data = [];
            foreach ($daftar_provinsi as $provinsi) {
                $values[] = "(?, ?, ?)";
                $data[] = 1;
                $name = str_replace('PROV. ', '', $provinsi->NAMA);
                $data[] = $name;
                $data[] = $provinsi->KODE_WILAYAH;
                $this->command->info("PROVINSI [{$name}] ADDED TO [INDONESIA]");
            }
            $this->command->warn("INSERTING PROVINSI");
            $values = implode(', ', $values);
            $sql = "INSERT INTO tbl_provinsi (pro_neg_id, pro_name, pro_kode) VALUES {$values};";
            \DB::insert($sql, $data);
            $this->command->warn("PROVINSI INSERTED");
        }
    }
}
