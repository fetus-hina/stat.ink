<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221121_104956_salmon3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // UNIQUE INDEX -> INDEX
        $this->dropIndex('salmon3_user_id_client_uuid', '{{%salmon3}}');
        $this->execute(
            'CREATE INDEX salmon3_user_id_client_uuid ON {{%salmon3}} ([[user_id]], [[client_uuid]]) ' .
            'WHERE ([[is_deleted]] = FALSE)'
        );

        // New index
        $this->execute(
            'CREATE INDEX salmon3_user_id_start_at ON {{%salmon3}} ([[user_id]], [[start_at]], [[id]]) ' .
            'WHERE ([[is_deleted]] = FALSE)'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('salmon3_user_id_start_at', '{{%salmon3}}');
        $this->dropIndex('salmon3_user_id_client_uuid', '{{%salmon3}}');
        $this->execute(
            'CREATE UNIQUE INDEX salmon3_user_id_client_uuid ON {{%salmon3}} ([[user_id]], [[client_uuid]]) ' .
            'WHERE ([[is_deleted]] = FALSE)'
        );

        return true;
    }
}
