<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230818_164754_create_index_user_auth_key_expires_at extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex(
            'user_auth_key_expires_at',
            'user_auth_key',
            ['expires_at', 'id'],
            true,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex(
            'user_auth_key_expires_at',
            'user_auth_key',
        );

        return true;
    }
}
