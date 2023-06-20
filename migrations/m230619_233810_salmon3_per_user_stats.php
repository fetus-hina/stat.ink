<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230619_233810_salmon3_per_user_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE ((%s))', [
                '[[salmon3_per_user_stats]]',
                '{{%salmon3}}',
                implode(', ', [
                    '[[user_id]]',
                    '[[schedule_id]]',
                    '[[id]]',
                ]),
                implode(') AND (', [
                    '[[is_deleted]] = FALSE',
                    '[[is_eggstra_work]] = FALSE',
                    '[[is_private]] = FALSE',
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('salmon3_per_user_stats', '{{%salmon3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
        ];
    }
}
