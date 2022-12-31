<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\SplatoonVersion;

class m160305_104512_battle_splatoon_version extends Migration
{
    public function safeUp()
    {
        // versions に取れるのはリリース時間とそのバージョンなので
        // battle テーブルに設定するのは「一つ前」のデータである必要がある
        $versions = SplatoonVersion::find()
            ->orderBy('[[released_at]] ASC')
            ->asArray()
            ->all();
        $lastVersionId = null;
        $lastReleasedAt = '2015-01-01T00:00:00+00:00'; // SQLを通すためだけの適当な過去
        foreach ($versions as $version) {
            $this->update(
                'battle',
                ['version_id' => $lastVersionId],
                ['and',
                    ['between', 'at', $lastReleasedAt, $version['released_at']],
                    ['version_id' => null],
                ],
            );
            $lastVersionId = $version['id'];
            $lastReleasedAt = $version['released_at'];
        }

        // 最新版で今後の予定がなければ新しいものがまだ null になっているはずなので
        // それを救済する
        $this->update(
            'battle',
            ['version_id' => $lastVersionId],
            ['and',
                ['>=', 'at', $lastReleasedAt],
                ['version_id' => null],
            ],
        );
    }

    public function safeDown()
    {
        $this->update('battle', ['version_id' => null]);
    }
}
