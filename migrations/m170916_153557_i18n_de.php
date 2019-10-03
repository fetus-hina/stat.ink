<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;

class m170916_153557_i18n_de extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            ['de-DE', 'Deutsch', 'German'],
        ]);

        $deDe = Language::findOne(['lang' => 'de-DE'])->id;
        $utf8 = Charset::findOne(['php_name' => 'UTF-8'])->id;
        $utf16 = Charset::findOne(['php_name' => 'UTF-16LE'])->id;
        $ansi = Charset::findOne(['php_name' => 'CP1252'])->id;

        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $deDe, $utf8, false ],
            [ $deDe, $utf16, false ],
            [ $deDe, $ansi, true ],
        ]);
    }

    public function safeDown()
    {
        $deDe = Language::findOne(['name' => 'de-DE'])->id;
        $this->delete('language_charset', ['language_id' => $deDe]);
        $this->delete('language', ['id' => $deDe]);
    }
}
