<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220916_020814_s3_111_time extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%splatoon_version3}}',
            ['release_at' => '2022-09-16T10:00:00+09:00'],
            ['tag' => '1.1.1']
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%splatoon_version3}}',
            ['release_at' => '2022-09-16T11:00:00+09:00'],
            ['tag' => '1.1.1']
        );

        return true;
    }
}
