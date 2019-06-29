<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170813_144240_fest_title extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[gender_id]] INTEGER NULL REFERENCES {{gender}}([[id]])',
            'ADD COLUMN [[fest_title_id]] INTEGER NULL REFERENCES {{fest_title}}([[id]])',
            'ADD COLUMN [[fest_exp]] INTEGER NULL',
            'ADD COLUMN [[fest_title_after_id]] INTEGER NULL REFERENCES {{fest_title}}([[id]])',
            'ADD COLUMN [[fest_exp_after]] INTEGER NULL',
        ]));
        $this->execute('ALTER TABLE {{battle_player2}} ' . implode(', ', [
            'ADD COLUMN [[gender_id]] INTEGER NULL REFERENCES {{gender}}([[id]])',
            'ADD COLUMN [[fest_title_id]] INTEGER NULL REFERENCES {{fest_title}}([[id]])',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[gender_id]]',
            'DROP COLUMN [[fest_title_id]]',
            'DROP COLUMN [[fest_exp]]',
            'DROP COLUMN [[fest_title_after_id]]',
            'DROP COLUMN [[fest_exp_after]]',
        ]));
        $this->execute('ALTER TABLE {{battle_player2}} ' . implode(', ', [
            'DROP COLUMN [[gender_id]]',
            'DROP COLUMN [[fest_title_id]]',
        ]));
    }
}
