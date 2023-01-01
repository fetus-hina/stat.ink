<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160724_122248_lang_charset extends Migration
{
    public function up()
    {
        $this->createTable('language_charset', [
            'language_id' => $this->integer()->notNull(),
            'charset_id' => $this->integer()->notNull(),
            'is_win_acp' => $this->boolean()->notNull(),
        ]);
        $this->addPrimaryKey('pk_language_charset', 'language_charset', ['language_id', 'charset_id']);
        $this->addForeignKey('fk_language_charset_1', 'language_charset', 'language_id', 'language', 'id');
        $this->addForeignKey('fk_language_charset_2', 'language_charset', 'charset_id', 'charset', 'id');
    }

    public function down()
    {
        $this->dropTable('language_charset');
    }
}
