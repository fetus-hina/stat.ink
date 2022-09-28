<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220928_080506_s3_1_1_2 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%splatoon_version3}}', [
            'tag' => '1.1.2',
            'group_id' => $this->key2id('{{%splatoon_version_group3}}', '1.1', 'tag'),
            'name' => 'v1.1.2',
            'release_at' => '2022-09-30T10:00:00+09:00',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%splatoon_version3}}', [
            'tag' => '1.1.2',
        ]);

        return true;
    }
}
