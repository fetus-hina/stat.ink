<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m190417_164517_update_lang_for_japanese_era extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{language}} ' . implode(', ', [
            'ALTER COLUMN [[lang]] TYPE VARCHAR(32)',
            'ADD COLUMN [[di]] JSONB NULL',
        ]));
        $this->insert('language', [
            'lang' => 'ja-JP@calendar=japanese',
            'name' => '日本語（和暦）',
            'name_en' => 'Japanese (Japanese Era)',
            'support_level_id' => 1, // FULL
            'di' => json_encode([
                'formatter' => [
                    'locale' => 'ja_JP@calendar=japanese',
                    'calendar' => IntlDateFormatter::TRADITIONAL,
                    'dateFormat' => 'Gy年MM月dd日',
                    'datetimeFormat' => 'Gy年MM月dd日 HH時mm分ss秒',
                    'timeFormat' => 'HH時mm分ss秒',
                ],
            ]),
        ]);
    }

    public function safeDown()
    {
        $this->delete('language', ['lang' => 'ja-JP@calendar=japanese']);
        $this->execute('ALTER TABLE {{language}} ' . implode(', ', [
            'ALTER COLUMN [[lang]] ' . $this->string(5)->notNull()->unique(),
            'DROP COLUMN [[di]]',
        ]));
    }
}
