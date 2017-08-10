<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;

class m170810_080542_french extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            ['fr-FR', 'Français (EU)', 'French (EU)'],
            ['fr-CA', 'Français (NA)', 'French (NA)'],
        ]);

        $frFr = Language::findOne(['lang' => 'fr-FR'])->id;
        $frCa = Language::findOne(['lang' => 'fr-CA'])->id;
        $utf8 = Charset::findOne(['php_name' => 'UTF-8'])->id;
        $utf16 = Charset::findOne(['php_name' => 'UTF-16LE'])->id;
        $ansi = Charset::findOne(['php_name' => 'CP1252'])->id;

        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $frFr, $utf8, false ],
            [ $frFr, $utf16, false ],
            [ $frFr, $ansi, true ],
            [ $frCa, $utf8, false ],
            [ $frCa, $utf16, false ],
            [ $frCa, $ansi, true ],
        ]);
    }

    public function safeDown()
    {
        $frFr = Language::findOne(['name' => 'fr-FR'])->id;
        $frCa = Language::findOne(['name' => 'fr-CA'])->id;
        $this->delete('language_charset', ['language_id' => [$frFr, $frCa]]);
        $this->delete('language', ['id' => [$frFr, $frCa]]);
    }
}
