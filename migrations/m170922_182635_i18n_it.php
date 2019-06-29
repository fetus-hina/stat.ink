<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;

class m170922_182635_i18n_it extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            ['it-IT', 'Italiano', 'Italian'],
        ]);

        $itIt = Language::findOne(['lang' => 'it-IT'])->id;
        $utf8 = Charset::findOne(['php_name' => 'UTF-8'])->id;
        $utf16 = Charset::findOne(['php_name' => 'UTF-16LE'])->id;
        $ansi = Charset::findOne(['php_name' => 'CP1252'])->id;

        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $itIt, $utf8, false ],
            [ $itIt, $utf16, false ],
            [ $itIt, $ansi, true ],
        ]);
    }

    public function safeDown()
    {
        $itIt = Language::findOne(['name' => 'it-IT'])->id;
        $this->delete('language_charset', ['language_id' => $itIt]);
        $this->delete('language', ['id' => $itIt]);
    }
}
