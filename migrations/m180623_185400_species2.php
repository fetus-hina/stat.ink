<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180623_185400_species2 extends Migration
{
    public function up()
    {
        $this->createTable('{{species2}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey()->notNull(),
            'name' => $this->string(32)->notNull(),
        ]);

        $this->batchInsert('{{species2}}', ['key', 'name'], [
            ['inkling', 'Inkling'],
            ['octoling', 'Octoling'],
        ]);

        $this->execute(
            'ALTER TABLE {{battle2}} ' .
            'ADD COLUMN [[species_id]] INTEGER NULL REFERENCES {{species2}}([[id]]) ',
        );

        $this->execute(
            'ALTER TABLE {{battle_player2}} ' .
            'ADD COLUMN [[species_id]] INTEGER NULL REFERENCES {{species2}}([[id]]) ',
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} DROP COLUMN [[species_id]]');
        $this->execute('ALTER TABLE {{battle_player2}} DROP COLUMN [[species_id]]');
        $this->dropTable('{{species2}}');
    }
}
