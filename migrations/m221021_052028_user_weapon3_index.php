<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221021_052028_user_weapon3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex(
            'user_weapon3_usage_key',
            '{{%user_weapon3}}',
            ['user_id', 'battles', 'last_used_at', 'weapon_id'],
            true, // user_id が pkey なので UNIQUE で問題ない
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('user_weapon3_usage_key', '{{%user_weapon3}}');

        return true;
    }
}
