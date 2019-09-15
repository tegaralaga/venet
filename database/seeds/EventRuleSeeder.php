<?php

use Illuminate\Database\Seeder;

class EventRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_event_rule;');
        $items = [
            [
                'eru_text_id' => 'Nantinya e-ticket ini harus ditukarkan menjadi gelang asli.',
                'eru_text_en' => 'This e-tiket must be exchanged with valid wristband(s) later.',
                'eru_general' => true,
            ],
            [
                'eru_text_id' => '1 orang hanya mendapatkan 1 tiket.',
                'eru_text_en' => '1 person get 1 ticket only.',
                'eru_general' => true,
            ],
            [
                'eru_text_id' => 'Tiket tidak dapat diperjualbelikan atau dipindahtangankan.',
                'eru_text_en' => 'Ticket cannot be traded or resold.',
                'eru_general' => true,
            ],
            [
                'eru_text_id' => 'E-ticket yang diterima sesuai dengan data yang didaftarkan.',
                'eru_text_en' => 'E-ticket that you will received according on the registered data.',
                'eru_general' => true,
            ],
            [
                'eru_text_id' => 'All weapons and illegal drugs are strictly prohibited.',
                'eru_text_en' => 'Dilarang membawa senjata tajam/api dan obat-obatan terlarang.',
                'eru_general' => true,
            ],
            [
                'eru_text_id' => 'The organizers have the right to refuse and/or discharge entry for those tickets holders who do not abide by the Terms and Condition implemented.',
                'eru_text_en' => 'Penyelenggara berhak untuk tidak memberikan izin masuk ke dalam tempat acara jika tidak mengikuti syarat-syarat dan ketentuan yang berlaku.',
                'eru_general' => true,
            ],
        ];
        foreach($items as $item) {
            $event_rule = new \App\Models\EventRuleModel($item);
            $event_rule->save();
        }
    }
}
