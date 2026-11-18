<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m261118_024854_ability3_gear_type extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->addColumn(
            '{{%ability3}}',
            'gear_type_id',
            (string)$this->pkRef('{{%gear_type}}')->null(),
        );

        foreach ($this->getData() as $gearTypeKey => $abilityKeys) {
            $this->update(
                '{{%ability3}}',
                ['gear_type_id' => $this->findGearTypeId($gearTypeKey)],
                ['key' => $abilityKeys],
            );
        }

        // The constraint cannot be appended to the column definition because the existing
        // primary-only rows would violate it until gear_type_id is filled in.
        $this->addCheck(
            'ability3_gear_type_id_check',
            '{{%ability3}}',
            '([[primary_only]] AND [[gear_type_id]] IS NOT NULL) OR (NOT [[primary_only]] AND [[gear_type_id]] IS NULL)',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropColumn('{{%ability3}}', 'gear_type_id');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%ability3}}',
        ];
    }

    /**
     * @return array<string, string[]>
     */
    private function getData(): array
    {
        return [
            'headgear' => [
                'comeback',
                'last_ditch_effort',
                'opening_gambit',
                'tenacity',
            ],
            'clothing' => [
                'ability_doubler',
                'haunt',
                'ninja_squid',
                'respawn_punisher',
                'thermal_ink',
            ],
            'shoes' => [
                'drop_roller',
                'object_shredder',
                'stealth_jump',
            ],
        ];
    }

    private function findGearTypeId(string $key): int
    {
        $id = new Query()
            ->select('id')
            ->from('{{%gear_type}}')
            ->andWhere(['key' => $key])
            ->scalar($this->db);

        return is_int($id) || (is_string($id) && ctype_digit($id))
            ? (int)$id
            : throw new RuntimeException("Unknown gear type: {$key}");
    }
}
