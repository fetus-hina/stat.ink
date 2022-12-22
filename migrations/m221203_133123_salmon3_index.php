<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m221203_133123_salmon3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $boolCond = fn (string $columnName, bool $value): string => vsprintf('%s = %s', [
            $db->quoteColumnName($columnName),
            $value ? 'TRUE' : 'FALSE',
        ]);
        $true = fn (string $columnName): string => $boolCond($columnName, true);
        $false = fn (string $columnName): string => $boolCond($columnName, false);

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE ((%s))', [
                $db->quoteColumnName('salmon3_start_at'),
                $db->quoteTableName('{{%salmon3}}'),
                implode(', ', [
                    $db->quoteColumnName('start_at'),
                ]),
                implode(') AND (', [
                    $false('has_broken_data'),
                    $false('has_disconnect'),
                    $false('is_deleted'),
                    $false('is_private'),
                    $true('is_automated'),
                ]),
            ]),
        );

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE ((%s))', [
                $db->quoteColumnName('salmon_schedule_weapon3_random_weapon'),
                $db->quoteTableName('{{%salmon_schedule_weapon3}}'),
                implode(', ', [
                    $db->quoteColumnName('schedule_id'),
                ]),
                implode(') AND (', [
                    '[[weapon_id]] IS NULL',
                    '[[random_id]] IS NOT NULL',
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
        $this->dropIndex('salmon_schedule_weapon3_random_weapon', '{{%salmon_schedule_weapon3}}');
        $this->dropIndex('salmon3_start_at', '{{%salmon3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3}}',
            '{{%salmon_schedule_weapon3}}',
        ];
    }
}
