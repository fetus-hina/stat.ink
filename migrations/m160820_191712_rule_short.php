<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160820_191712_rule_short extends Migration
{
    public function up()
    {
        $data = [
            'nawabari' => 'TW',
            'area' => 'SZ',
            'yagura' => 'TC',
            'hoko' => 'RM',
        ];

        $this->execute('ALTER TABLE {{rule}} ADD COLUMN [[short_name]] VARCHAR(16)');
        foreach ($data as $key => $name) {
            $this->update('rule', ['short_name' => $name], ['key' => $key]);
        }
        $this->execute('ALTER TABLE {{rule}} ALTER COLUMN [[short_name]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{rule}} DROP COLUMN [[short_name]]');
    }
}
