<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m230401_084855_battle3_aggregate_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE %s', [
                $db->quoteColumnName('battle3_aggregatable'),
                $db->quoteTableName('{{%battle3}}'),
                $db->quoteColumnName('start_at'),
                implode(' AND ', [
                    '[[has_disconnect]] = FALSE',
                    '[[is_automated]] = TRUE',
                    '[[is_deleted]] = FALSE',
                    '[[use_for_entire]] = TRUE',
                    '[[start_at]] IS NOT NULL',
                    '[[end_at]] IS NOT NULL',
                    '[[start_at]] < [[end_at]]',
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
        $this->dropIndex('battle3_aggregatable', '{{%battle3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return ['{{%battle3}}'];
    }
}
