<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m230408_190231_eggstra_schedule extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute('ALTER TABLE {{%salmon_schedule3}} DROP CONSTRAINT [[salmon_schedule3_start_at_key]]');
        $this->addColumn(
            '{{%salmon_schedule3}}',
            'is_eggstra_work',
            (string)$this->boolean()->notNull()->defaultValue(false),
        );
        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE %s = FALSE', [
                $db->quoteColumnName('salmon_schedule3_normal_start_at'),
                $db->quoteTableName('{{%salmon_schedule3}}'),
                $db->quoteColumnName('start_at'),
                $db->quoteColumnName('[[is_eggstra_work]]'),
            ]),
        );
        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE %s = TRUE', [
                $db->quoteColumnName('salmon_schedule3_eggstra_start_at'),
                $db->quoteTableName('{{%salmon_schedule3}}'),
                $db->quoteColumnName('start_at'),
                $db->quoteColumnName('[[is_eggstra_work]]'),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('salmon_schedule3_eggstra_start_at', '{{%salmon_schedule3}}');
        $this->dropIndex('salmon_schedule3_normal_start_at', '{{%salmon_schedule3}}');
        $this->dropColumn('{{%salmon_schedule3}}', 'is_eggstra_work');
        $this->execute(
            'ALTER TABLE {{%salmon_schedule3}} ' .
            'ADD CONSTRAINT [[salmon_schedule3_start_at_key]] UNIQUE ([[start_at]])',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_schedule3}}',
        ];
    }
}
