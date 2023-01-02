<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;

class m171104_130148_ability2_splatnet extends Migration
{
    public function up()
    {
        $this->addColumn('ability2', 'splatnet', 'INTEGER UNIQUE NULL');
        $this->update('ability2', $this->createUpdate(), [
            'key' => array_keys($this->getData()),
        ]);
        $this->execute('ALTER TABLE {{ability2}} ALTER COLUMN [[splatnet]] SET NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('ability2', 'splatnet');
    }

    public function createUpdate(): array
    {
        return [
            'splatnet' => new Expression(sprintf(
                '(CASE %s %s END)',
                $this->db->quoteColumnName('key'),
                implode(' ', array_map(
                    fn (string $key, int $value): string => sprintf(
                        'WHEN %s THEN %d',
                        $this->db->quoteValue($key),
                        $value,
                    ),
                    array_keys($this->getData()),
                    array_values($this->getData()),
                )),
            )),
        ];
    }

    public function getData(): array
    {
        return [
            'ability_doubler' => 108,
            'bomb_defense_up' => 12,
            'cold_blooded' => 13,
            'comeback' => 103,
            'drop_roller' => 111,
            'haunt' => 105,
            'ink_recovery_up' => 2,
            'ink_resistance_up' => 11,
            'ink_saver_main' => 0,
            'ink_saver_sub' => 1,
            'last_ditch_effort' => 101,
            'ninja_squid' => 104,
            'object_shredder' => 110,
            'opening_gambit' => 100,
            'quick_respawn' => 8,
            'quick_super_jump' => 9,
            'respawn_punisher' => 107,
            'run_speed_up' => 3,
            'special_charge_up' => 5,
            'special_power_up' => 7,
            'special_saver' => 6,
            'stealth_jump' => 109,
            'sub_power_up' => 10,
            'swim_speed_up' => 4,
            'tenacity' => 102,
            'thermal_ink' => 106,
        ];
    }
}
