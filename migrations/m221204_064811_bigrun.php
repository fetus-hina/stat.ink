<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221204_064811_bigrun extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('ALTER TABLE %s %s', [
                $this->db->quoteTableName('{{%salmon_schedule3}}'),
                implode(', ', [
                    'ALTER COLUMN [[map_id]] DROP NOT NULL',
                    'ADD COLUMN [[big_map_id]] ' . (string)$this->pkRef('{{%map3}}', 'id')->null(),
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
        $this->execute(
            vsprintf('ALTER TABLE %s %s', [
                $this->db->quoteTableName('{{%salmon_schedule3}}'),
                implode(', ', [
                    'ALTER COLUMN [[map_id]] SET NOT NULL',
                    'DROP COLUMN [[big_map_id]]',
                ]),
            ]),
        );

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_schedule3}}',
        ];
    }
}
