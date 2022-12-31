<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;
use yii\helpers\ArrayHelper;

class m171125_144954_lang extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            ['nl-NL', 'Nederlands', 'Dutch'],
            ['ru-RU', 'Русский', 'Russian'],
        ]);
        $this->insert('charset', [
            'name'          => 'Windows-1251',
            'php_name'      => 'CP1251',
            'substitute'    => 63,
            'is_unicode'    => false,
            'order'         => 13,
        ]);

        $nl = Language::findOne(['lang' => 'nl-NL'])->id;
        $ru = Language::findOne(['lang' => 'ru-RU'])->id;

        $c = ArrayHelper::map(
            Charset::find()->asArray()->all(),
            'php_name',
            'id',
        );

        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $nl, $c['UTF-8'],     false ],
            [ $nl, $c['UTF-16LE'],  false ],
            [ $nl, $c['CP1252'],    true  ],
            [ $ru, $c['UTF-8'],     false ],
            [ $ru, $c['UTF-16LE'],  false ],
            [ $ru, $c['CP1251'],    true  ],
        ]);
    }

    public function safeDown()
    {
        $nl = Language::findOne(['lang' => 'nl-NL'])->id;
        $ru = Language::findOne(['lang' => 'ru-RU'])->id;

        $this->delete('language_charset', ['language_id' => [$nl, $ru]]);
        $this->delete('language', ['id' => [$nl, $ru]]);
        $this->delete('charset', ['php_name' => 'CP1251']);
    }
}
