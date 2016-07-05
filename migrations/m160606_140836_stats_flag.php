<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160606_140836_stats_flag extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[use_for_entire]] BOOLEAN NOT NULL DEFAULT FALSE',
        ]));
        $this->execute('UPDATE {{battle}} SET [[use_for_entire]] = TRUE WHERE [[is_automated]] = TRUE');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[use_for_entire]]');
    }
}
