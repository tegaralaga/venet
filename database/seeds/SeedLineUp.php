<?php

use Illuminate\Database\Seeder;

class SeedLineUp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE tbl_line_up;');
        \DB::statement('TRUNCATE tbl_line_up_contact;');
        \DB::statement('TRUNCATE tbl_line_up_member;');
        $item = [
            'lin_lty_id' => 0,
            'lin_name' => null,
            'lin_description' => null,
            'contacts' => [],
            'children' => [],
        ];
        $kaisar_kinanti = $item;
        $kaisar_kinanti['lin_lty_id'] = 'Nonprofit Organization';
        $kaisar_kinanti['lin_name'] = 'KaisarKinanti';
        $kaisar_kinanti_mobile_number = new \App\Helpers\Contact\ContactMobileNumber('+62 812-1216-8724', 'line_up');
        $kaisar_kinanti['contacts'][] = $kaisar_kinanti_mobile_number;
        $bias_tegaralaga = $item;
        $bias_tegaralaga['lin_lty_id'] = 'Software Engineer';
        $bias_tegaralaga['lin_name'] = 'Bias Tegaralaga';
        $bias_tegaralaga_mobile_number = new \App\Helpers\Contact\ContactMobileNumber('+62 812-6069-5203', 'line_up');
        $bias_tegaralaga['contacts'][] = $bias_tegaralaga_mobile_number;
        $gusna_raisa = $item;
        $gusna_raisa['lin_lty_id'] = 'Penyanyi';
        $gusna_raisa['lin_name'] = 'Gusna Raisa';
        $gusna_raisa_mobile_number = new \App\Helpers\Contact\ContactMobileNumber('+62 812-2195-0648', 'line_up');
        $gusna_raisa['contacts'][] = $gusna_raisa_mobile_number;
        $kaisar_kinanti['children'][] = $bias_tegaralaga;
        $kaisar_kinanti['children'][] = $gusna_raisa;
        $didi_kempot = $item;
        $didi_kempot['lin_lty_id'] = 'Penyanyi';
        $didi_kempot['lin_name'] = 'Didi Kempot';
        $items = [];
        $items[] = $kaisar_kinanti;
        $items[] = $didi_kempot;
        $this->insert($items);
    }

    public function insert($array) {
        foreach ($array as $item) {
            $this->processLineUp($item);
        }
    }

    public function processLineUp($item, \App\Models\LineUpModel $parent = null) {
        $parent_id = 0;
        if (!($parent == null)) {
            $parent_id = $parent->lin_id;
        }
        $line_up_type = \App\Models\LineUpTypeModel::select('lty_id')->where('lty_name', $item['lin_lty_id'])->first();
        if (!($line_up_type == null)) {
            $data = $item;
            $data['lin_lty_id'] = $line_up_type->lty_id;
            unset($data['contacts']);
            unset($data['children']);
            $line_up = new \App\Models\LineUpModel($data);
            if ($line_up->save()) {
                foreach ($item['children'] as $children) {
                    $this->processLineUp($children, $line_up);
                }
                $this->processLineUpContact($item['contacts'], $line_up);
                if ($parent_id > 0) {
                    $this->mapLineUpMember($line_up->lin_id, $parent_id);
                }
            }
        }
    }

    public function mapLineUpMember($line_up_id, $belong_to) {
        $line_up_member = new \App\Models\LineUpMemberModel([
            'lme_lin_id' => $line_up_id,
            'lme_belong_to' => $belong_to,
        ]);
        if (!$line_up_member->save()) {
            $this->command->error('FAILED SAVING MEMBERSHIP');
        }
    }

    public function processLineUpContact($line_up_contacts, \App\Models\LineUpModel $parent = null) {
        foreach ($line_up_contacts as $contact) {
            $contact->setParent($parent->lin_id);
            if(!$contact->save()) {
                $this->command->error('FAILED SAVING CONTACT');
            }
        }
    }
}
