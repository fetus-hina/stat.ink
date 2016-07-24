<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\Charset;
use app\models\Language;
use yii\db\Migration;

class m160724_123800_lang_charset_data extends Migration
{
    public function safeUp()
    {
        $langs = $this->getLanguages();
        $charsets = $this->getCharsets();
        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $langs['en-US'], $charsets['UTF-8'], false ],

            [ $langs['en-GB'], $charsets['UTF-8'], false ],

            [ $langs['ja-JP'], $charsets['UTF-8'], false ],
            [ $langs['ja-JP'], $charsets['CP932'], true ],

            [ $langs['es-ES'], $charsets['UTF-8'], false ],
            [ $langs['es-ES'], $charsets['CP1252'], true ],

            [ $langs['es-MX'], $charsets['UTF-8'], false ],
            [ $langs['es-MX'], $charsets['CP1252'], true ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('language_charset');
    }

    private function getLanguages()
    {
        $ret = [];
        foreach (Language::find()->asArray()->all() as $row) {
            $ret[$row['lang']] = $row['id'];
        }
        return $ret;
    }

    private function getCharsets()
    {
        $ret = [];
        foreach (Charset::find()->asArray()->all() as $row) {
            $ret[$row['php_name']] = $row['id'];
        }
        return $ret;
    }
}
