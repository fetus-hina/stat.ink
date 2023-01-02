<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180328_124400_lang_support_level extends Migration
{
    public function up()
    {
        $this->createTable('{{support_level}}', [
            'id' => $this->integer()->notNull(),
            'name' => $this->string(32)->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);
        $this->batchInsert('{{support_level}}', ['id', 'name'], [
            [1, 'Full'],
            [2, 'Almost'],
            [3, 'Partial'],
            [4, 'Few'],
        ]);
        $this->execute(
            'ALTER TABLE {{language}} ' .
            'ADD COLUMN [[support_level_id]] INTEGER REFERENCES {{support_level}} ([[id]])',
        );
        $this->update('{{language}}', ['support_level_id' => 1], ['lang' => 'ja-JP']);
        $this->update('{{language}}', ['support_level_id' => 2], ['lang' => ['en-US', 'en-GB']]);
        $this->update('{{language}}', ['support_level_id' => 3], ['lang' => ['es-ES', 'es-MX']]);
        $this->update('{{language}}', ['support_level_id' => 4], ['support_level_id' => null]);
        $this->execute('ALTER TABLE {{language}} ALTER COLUMN [[support_level_id]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{language}} DROP COLUMN [[support_level_id]]');
        $this->dropTable('{{support_level}}');
    }
}
