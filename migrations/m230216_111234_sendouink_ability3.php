<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m230216_111234_sendouink_ability3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%ability3}}', 'sendouink', (string)$this->string(3)->unique());
        $this->execute($this->getUpdateSql());
        $this->alterColumn('{{%ability3}}', 'sendouink', (string)$this->string(3)->unique()->notNull());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ability3}}', 'sendouink');

        return true;
    }

    private function getUpdateSql(): string
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $data = $this->getData();

        return vsprintf('UPDATE %s SET %s = %s', [
            $db->quoteTableName('{{%ability3}}'),
            $db->quoteColumnName('sendouink'),
            vsprintf('CASE %s %s END', [
                $db->quoteColumnName('key'),
                implode(
                    ' ',
                    array_map(
                        fn (string $key, string $value): string => vsprintf('WHEN %s THEN %s', [
                            $db->quoteValue($key),
                            $db->quoteValue($value),
                        ]),
                        array_keys($data),
                        array_values($data),
                    ),
                ),
            ]),
        ]);
    }

    private function getData(): array
    {
        return [
            'ink_saver_main' => 'ISM',
            'ink_saver_sub' => 'ISS',
            'ink_recovery_up' => 'IRU',
            'run_speed_up' => 'RSU',
            'swim_speed_up' => 'SSU',
            'special_charge_up' => 'SCU',
            'special_saver' => 'SS',
            'special_power_up' => 'SPU',
            'quick_respawn' => 'QR',
            'quick_super_jump' => 'QSJ',
            'sub_power_up' => 'BRU',
            'ink_resistance_up' => 'RES',
            'sub_resistance_up' => 'SRU',
            'intensify_action' => 'IA',
            'opening_gambit' => 'OG',
            'last_ditch_effort' => 'LDE',
            'tenacity' => 'T',
            'comeback' => 'CB',
            'ninja_squid' => 'NS',
            'haunt' => 'H',
            'thermal_ink' => 'TI',
            'respawn_punisher' => 'RP',
            'ability_doubler' => 'AD',
            'stealth_jump' => 'SJ',
            'object_shredder' => 'OS',
            'drop_roller' => 'DR',
        ];
    }
}
