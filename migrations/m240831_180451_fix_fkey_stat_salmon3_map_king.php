<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240831_180451_fix_fkey_stat_salmon3_map_king extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('ALTER TABLE %s %s', [
                '{{%stat_salmon3_map_king}}',
                implode(', ', [
                    'DROP CONSTRAINT {{%stat_salmon3_map_king_map_id_fkey}}',
                    'ADD FOREIGN KEY ([[map_id]]) REFERENCES {{%salmon_map3}} ([[id]])',
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
                '{{%stat_salmon3_map_king}}',
                implode(', ', [
                    'DROP CONSTRAINT {{%stat_salmon3_map_king_map_id_fkey}}',
                    'ADD FOREIGN KEY ([[map_id]]) REFERENCES {{%map3}} ([[id]])',
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_salmon3_map_king}}',
        ];
    }
}
