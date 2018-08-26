<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;
use app\models\SupportLevel;
use yii\helpers\ArrayHelper;

class m180826_212102_chinese extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en', 'support_level_id'], [
            ['zh-CN', '简体中文', 'Chinese (Simplified)', SupportLevel::PARTIAL],
            ['zh-TW', '繁體中文', 'Chinese (Traditional)', SupportLevel::PARTIAL],
        ]);
        $this->batchInsert('charset', ['name', 'php_name', 'substitute', 'is_unicode', 'order'], [
            ['GB 2312', 'EUC-CN', 63, false, 14],
            ['Big5', 'BIG-5', 63, false, 15],
        ]);

        $cn = Language::findOne(['lang' => 'zh-CN'])->id;
        $tw = Language::findOne(['lang' => 'zh-TW'])->id;

        $c = ArrayHelper::map(
            Charset::find()->asArray()->all(),
            'php_name',
            'id'
        );
            
        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [ $cn, $c['UTF-8'],     false ],
            [ $cn, $c['UTF-16LE'],  false ],
            [ $cn, $c['EUC-CN'],    true  ],
            [ $tw, $c['UTF-8'],     false ],
            [ $tw, $c['UTF-16LE'],  false ],
            [ $tw, $c['BIG-5'],     true  ],
        ]);
    }

    public function safeDown()
    {
        $cn = Language::findOne(['lang' => 'zh-CN'])->id;
        $tw = Language::findOne(['lang' => 'zh-TW'])->id;

        $this->delete('language_charset', ['language_id' => [$cn, $tw]]);
        $this->delete('language', ['id' => [$cn, $tw]]);
        $this->delete('charset', ['php_name' => ['EUC-CN', 'BIG-5']]);
    }
}
