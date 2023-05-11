<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Query;

final class m230510_222502_battle3_x_power_history_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $lobbyId = TypeHelper::int(
            (new Query())
                ->select(['id'])
                ->from('{{%lobby3}}')
                ->andWhere(['{{%lobby3}}.[[key]]' => 'xmatch'])
                ->limit(1)
                ->scalar(),
        );

        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE (%s)', [
                $db->quoteTableName('battle3_x_power_history'),
                '{{%battle3}}',
                implode(', ', [
                    $db->quoteColumnName('user_id'),
                    $db->quoteColumnName('rule_id'),
                    $db->quoteColumnName('start_at'),
                    $db->quoteColumnName('id'),
                ]),
                implode(') AND (', [
                    "[[lobby_id]] = {$lobbyId}",
                    '[[is_deleted]] = FALSE',
                    '[[rule_id]] IS NOT NULL',
                    '[[start_at]] IS NOT NULL',
                    '([[x_power_after]] IS NOT NULL OR [[x_power_before]] IS NOT NULL)',
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
        $this->dropIndex('battle3_x_power_history', '{{%battle3}}');

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
