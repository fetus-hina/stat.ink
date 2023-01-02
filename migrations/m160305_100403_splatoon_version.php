<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160305_100403_splatoon_version extends Migration
{
    public function up()
    {
        $this->createTable('splatoon_version', [
            'id' => $this->primaryKey(),
            'tag' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull(),
            'released_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->createIndex('ix_splatoon_version_1', 'splatoon_version', 'released_at');

        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[version_id]] INTEGER REFERENCES {{splatoon_version}} ( [[id]] )',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[version_id]]');
        $this->dropTable('splatoon_version');
    }
}
