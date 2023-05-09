<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m230508_055205_x_power_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $lobbyId = (new Query())
            ->select(['id'])
            ->from('{{%lobby3}}')
            ->andWhere(['key' => 'xmatch'])
            ->limit(1)
            ->scalar();
        if ($lobbyId === null) {
            return false;
        }

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE %s', [
                $db->quoteTableName('battle3_xpower_key'),
                $db->quoteTableName('{{%battle3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $column): string => $db->quoteColumnName($column),
                        ['user_id', 'rule_id', 'end_at'],
                    ),
                ),
                implode(' AND ', [
                    '[[end_at]] IS NOT NULL',
                    '[[is_deleted]] = FALSE',
                    '[[lobby_id]] = ' . $db->quoteValue($lobbyId),
                    '[[period]] IS NOT NULL',
                    'COALESCE([[x_power_after]], [[x_power_before]]) IS NOT NULL',
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
        $this->dropIndex('battle3_xpower_key', '{{%battle3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%battle3}}',
        ];
    }
}
