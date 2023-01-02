<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Charset;
use app\models\Language;
use yii\db\Migration;

class m160725_063530_utf16le extends Migration
{
    public function safeUp()
    {
        $this->insert('charset', [
            'name' => 'UTF-16LE',
            'php_name' => 'UTF-16LE',
            'substitute' => ord('?'),
        ]);
        $id = Charset::findOne(['php_name' => 'UTF-16LE'])->id;
        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ Language::findOne(['lang' => 'en-US'])->id, $id, false ],
            [ Language::findOne(['lang' => 'en-GB'])->id, $id, false ],
            [ Language::findOne(['lang' => 'es-ES'])->id, $id, false ],
            [ Language::findOne(['lang' => 'es-MX'])->id, $id, false ],
            [ Language::findOne(['lang' => 'ja-JP'])->id, $id, false ],
        ]);
    }

    public function safeDown()
    {
        $id = Charset::findOne(['php_name' => 'UTF-16LE'])->id;
        $this->delete('language_charset', ['charset_id' => $id]);
        $this->delete('charset', ['id' => $id]);
    }
}
