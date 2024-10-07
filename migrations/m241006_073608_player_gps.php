<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m241006_073608_player_gps extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableMap = [
            '{{%battle_player_gear_power3}}' => '{{%battle_player3}}',
            '{{%battle_tricolor_player_gear_power3}}' => '{{%battle_tricolor_player3}}',
        ];
        foreach ($tableMap as $tableName => $srcTable) {
            $this->createTable($tableName, [
                'id' => $this->bigPrimaryKey()->notNull(),
                'player_id' => $this->bigPkRef($srcTable)->notNull(),
                'ability_id' => $this->pkRef('{{%ability3}}')->notNull(),
                'gear_power' => $this->integer()->notNull(),

                'UNIQUE ([[player_id]], [[ability_id]])',
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%battle_player_gear_power3}}',
            '{{%battle_tricolor_player_gear_power3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%battle_player_gear_power3}}',
            '{{%battle_tricolor_player_gear_power3}}',
        ];
    }
}
