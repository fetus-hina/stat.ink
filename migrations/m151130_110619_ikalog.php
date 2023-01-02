<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151130_110619_ikalog extends Migration
{
    public function up()
    {
        $this->createTable('ikalog_version', [
            'id' => $this->primaryKey(),
            'revision' => sprintf('CHAR(%d) NOT NULL UNIQUE', strlen(hash('sha1', ''))),
            'summary' => $this->text(),
            'at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);

        $this->createTable('winikalog_version', [
            'id' => $this->primaryKey(),
            'revision_id' => $this->integer(),
            'build_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->addForeignKey('fk_winikalog_version_1', 'winikalog_version', 'revision_id', 'ikalog_version', 'id');
    }

    public function down()
    {
        $this->dropTable('winikalog_version');
        $this->dropTable('ikalog_version');
    }
}
