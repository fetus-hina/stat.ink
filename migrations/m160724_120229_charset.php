<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160724_120229_charset extends Migration
{
    public function up()
    {
        $this->createTable('charset', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(32)->notNull(),
            'php_name'      => $this->string(32)->notNull(),
            'substitute'    => $this->integer()->notNull()->defaultValue(ord('?')),
        ]);
        $this->batchInsert('charset', ['name', 'php_name', 'substitute'], [
            [ 'UTF-8', 'UTF-8', ord('?') ],
            [ 'Shift-JIS', 'CP932', 0x3013 ],
            [ 'Windows-1252', 'CP1252', ord('?') ],
        ]);
    }

    public function down()
    {
        $this->dropTable('charset');
    }
}
