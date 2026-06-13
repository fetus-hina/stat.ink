<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260613_070109_salmon3_created_at extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->execute(
            'CREATE INDEX salmon3_created_at ON {{%salmon3}} ([[created_at]]) ' .
            'WHERE [[is_deleted]] = FALSE',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropIndex('salmon3_created_at', '{{%salmon3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3}}',
        ];
    }
}
