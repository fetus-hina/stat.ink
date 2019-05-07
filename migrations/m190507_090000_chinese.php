<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\models\Charset;
use app\models\Language;
use app\models\SupportLevel;
use yii\helpers\ArrayHelper;

class m190507_090000_chinese extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en', 'support_level_id'], [
            ['zh-CN', '简体中文', 'Chinese (Simplified)', SupportLevel::PARTIAL],
            ['zh-TW', '繁體中文', 'Chinese (Traditional)', SupportLevel::PARTIAL],
            [
                'zh-TW@calendar=roc',
                '繁體中文（民國紀年）',
                'Chinese (Traditional, ROC Era)',
                SupportLevel::PARTIAL,
            ],
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

        $this->batchInsert('accept_language', ['rule', 'language_id'], [
            [ 'zh-hans', $cn ], // Generic Simplified
            [ 'zh-hant', $tw ], // Generic Traditional

            [ 'zh-cn', $cn ], // China, People's Repblic
            [ 'zh-sg', $cn ], // Singapore

            [ 'zh*', $tw ],   // Taiwan, Hong Kong, Macao
        ]);
    }

    public function safeDown()
    {
        foreach (['zh-CN', 'zh-TW', 'zh-TW@calendar=roc'] as $langId) {
            $lang = Language::findOne(['lang' => $langId])->id;
            $this->delete('accept_language', ['language_id' => $lang]);
            $this->delete('language_charset', ['language_id' => $lang]);
            $this->delete('language', ['id' => $lang]);
        }
        $this->delete('charset', ['php_name' => ['EUC-CN', 'BIG-5']]);
    }
}
