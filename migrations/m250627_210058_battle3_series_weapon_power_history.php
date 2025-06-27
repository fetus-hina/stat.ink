<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m250627_210058_battle3_series_weapon_power_history extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $db = $this->getDb();
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE (%s)', [
                $db->quoteTableName('{{%battle3_series_weapon_power_history}}'),
                $db->quoteTableName('{{%battle3}}'),
                implode(', ', [
                    $db->quoteColumnName('user_id'),
                    $db->quoteColumnName('weapon_id'),
                    $db->quoteColumnName('end_at') . ' DESC',
                    $db->quoteColumnName('id') . ' DESC',
                ]),
                implode(') AND (', [
                    '[[is_deleted]] = FALSE',
                    '[[end_at]] IS NOT NULL',
                    'COALESCE([[series_weapon_power_after]], [[series_weapon_power_before]]) IS NOT NULL',
                    vsprintf('%s >= %s::timestamptz', [
                        $db->quoteColumnName('end_at'),
                        $db->quoteValue('2025-06-12T01:00:00+00:00'),
                    ]),
                    vsprintf('%s = %d', [
                        $db->quoteColumnName('lobby_id'),
                        $this->key2id('{{%lobby3}}', 'bankara_challenge'),
                    ]),
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropIndex('{{%battle3}}', '{{%battle3_series_weapon_power_history}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%battle3}}',
        ];
    }
}
