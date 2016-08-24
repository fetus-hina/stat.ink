<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160824_190053_user_stat_nawabari extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user_stat}} ' . implode(', ', [
            'ADD COLUMN [[nawabari_inked]] BIGINT NOT NULL DEFAULT 0',
            'ADD COLUMN [[nawabari_inked_max]] BIGINT NOT NULL DEFAULT 0',
            'ADD COLUMN [[nawabari_inked_battle]] BIGINT NOT NULL DEFAULT 0',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user_stat}} ' . implode(', ', [
            'DROP COLUMN [[nawabari_inked]]',
            'DROP COLUMN [[nawabari_inked_max]]',
            'DROP COLUMN [[nawabari_inked_battle]]',
        ]));
    }
}
