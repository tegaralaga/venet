<?php

use Illuminate\Database\Seeder;

class AddWilayah extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filename = getcwd() . env('IMPORT_WILAYAH_FILE', '');
        $this->command->warn("IMPORT FILE [{$filename}] TO DATABASE");
        \DB::statement('DROP TABLE IF EXISTS a_id_territory;');
        if (!file_exists($filename)) {
            die("{$filename} does not exists!");
        }
        $templine = '';
        $lines = file($filename);
        foreach ($lines as $line)
        {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';')
            {
                $statements = ['create', 'alter'];
                $substr = substr($templine, 0, 100);
                $explode = explode(' ', $substr);
                $start = strtolower(trim($explode[0]));
                $statement = false;
                if (in_array($start, $statements)) {
                    $statement = true;
                }
                if ($statement) {
                    \DB::statement($templine);
                } else {
                    switch ($start) {
                        case 'insert':
                            \DB::insert($templine);
                            break;
                        case 'update':
                            \DB::update($templine);
                            break;
                        case 'delete':
                            \DB::delete($templine);
                            break;
                    }
                }
                $templine = '';
            }
        }
        $this->command->info("FILE [{$filename}] IMPORTED");
    }
}
