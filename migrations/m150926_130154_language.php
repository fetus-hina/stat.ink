<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150926_130154_language extends Migration
{
    public function up()
    {
        $this->createTable('language', [
            'id' => $this->primaryKey(),
            'lang' => $this->string(5)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
            'name_en' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            [ 'en-US', 'English(US)', 'English(US)' ],
            [ 'ja-JP', '日本語', 'Japanese' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('language');
    }
}
