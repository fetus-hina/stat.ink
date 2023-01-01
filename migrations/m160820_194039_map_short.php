<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160820_194039_map_short extends Migration
{
    public function up()
    {
        $data = [
            'arowana' => 'Mall',
            'bbass' => 'Skatepark',
            'shionome' => 'Rig',
            'dekaline' => 'Underpass',
            'hakofugu' => 'Warehouse',
            'hokke' => 'Port',
            'mozuku' => 'Dome',
            'negitoro' => 'Depot',
            'tachiuo' => 'Towers',
            'mongara' => 'Camp',
            'hirame' => 'Heights',
            'masaba' => 'Bridge',
            'kinmedai' => 'Museum',
            'mahimahi' => 'Mahi-Mahi',
            'shottsuru' => 'Pit',
            'anchovy' => 'Ancho-V',
        ];

        $this->execute('ALTER TABLE {{map}} ADD COLUMN [[short_name]] VARCHAR(16)');
        foreach ($data as $key => $name) {
            $this->update('map', ['short_name' => $name], ['key' => $key]);
        }
        $this->execute('ALTER TABLE {{map}} ALTER COLUMN [[short_name]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{map}} DROP COLUMN [[short_name]]');
    }
}
