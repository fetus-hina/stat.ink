<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160606_142124_ikalog_requirements extends Migration
{
    public function up()
    {
        $this->createTable('ikalog_requirement', [
            'id'            => $this->primaryKey(),
            'from'          => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            'version_date'  => 'VARCHAR(64) NOT NULL',
        ]);
        $this->batchInsert('ikalog_requirement', ['from', 'version_date'], [
            [ '2016-06-08 10:00:00+09:00', '2016-06-08_00' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('ikalog_requirement');
    }
}
