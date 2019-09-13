<?php

use Illuminate\Database\Seeder;

class ImportWilayah extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = getcwd() . env('IMPORT_WILAYAH_FILE', '/wilayah/data-semua-wilayah-indonesia-000000-20190428131634.sql');
        if (!file_exists($file)) {
            if (!file_exists(dirname($file))) {
                $this->command->warn('CREATING DIRECTORY [' . dirname($file) . ']');
                if (!mkdir(dirname($file))) {
                    $this->command->error('CANNOT CREATE DIRECTORY [' . dirname($file) . ']');
                    exit;
                }
                $this->command->info('DIRECTORY [' . dirname($file) . '] CREATED');
            }
            $url = env('IMPORT_WILAYAH_URL', 'https://www.dropbox.com/s/ze6kgp7wn43kalv/data-semua-wilayah-indonesia-000000-20190428131634.sql?dl=1');
            $this->command->warn("DOWNLOADING FILE [{$url}]");
            $download = file_get_contents($url);
            $this->command->info("FILE [{$url}] DOWNLOADED");
            $this->command->warn("WRITE TO FILE [{$file}]");
            $handle = fopen($file, 'w');
            if ($handle == false) {
                $this->command->error("CANNOT OPEN FILE {$file}");
                exit;
            }
            fwrite($handle, $download);
            fclose($handle);
            $this->command->info("FILE [{$file}] WRITTEN");
        }
        $this->call([
            AddWilayah::class,
            SeedNegara::class,
            SeedProvinsi::class,
            SeedKota::class,
            SeedKecamatan::class,
            SeedKelurahan::class,
            RemoveWilayah::class,
        ]);
    }
}
