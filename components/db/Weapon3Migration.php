<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use yii\db\Expression;
use yii\db\Query;

use function array_reduce;
use function in_array;
use function intval;
use function preg_match;

/**
 * @phpstan-type type3_string1 'blaster'|'brella'|'brush'|'charger'|'maneuver'|'reelgun'|'roller'
 * @phpstan-type type3_string2 'shooter'|'slosher'|'spinner'|'stringer'|'wiper'
 * @phpstan-type sub3_string1 'curlingbomb'|'jumpbeacon'|'kyubanbomb'|'linemarker'|'pointsensor'
 * @phpstan-type sub3_string2 'poisonmist'|'quickbomb'|'robotbomb'|'splashbomb'|'splashshield'
 * @phpstan-type sub3_string3 'sprinkler'|'tansanbomb'|'torpedo'|'trap'
 * @phsptan-type sp3_string1 'amefurashi'|'energystand'|'greatbarrier'|'hopsonar'|'jetpack'
 * @phpstan-type sp3_string2 'kanitank'|'kyuinki'|'megaphone'|'missile'|'nicedama'|'sameride'
 * @phpstan-type sp3_string3 'shokuwander'|'tripletornado'|'ultrahanko'|'ultrashot'|'teioika'|'decoy'
 *
 * @phpstan-type type3_string type3_string1|type3_string2
 * @phpstan-type sub3_string sub3_string1|sub3_string2|sub3_string3
 * @phpstan-type sp3_string sp3_string1|sp3_string2|sp3_string3
 */
trait Weapon3Migration
{
    use AutoKey;

    /**
     * @phpstan-param non-empty-string $key
     * @phpstan-param non-empty-string $name
     * @phpstan-param type3_string $type
     * @phpstan-param sub3_string|null $sub
     * @phpstan-param sp3_string|null $special
     * @phpstan-param non-empty-string|null $main
     * @phpstan-param non-empty-string|null $canonical
     * @phpstan-param 'A+'|'A-'|'B'|'C+'|'C-'|'D+'|'D-'|'E+'|'E-'|null $xGroup
     * @phpstan-param 'S'|'M'|'L'|'C'|null $xGroup2
     */
    protected function upWeapon3(
        string $key,
        string $name,
        string $type,
        ?string $sub = null,
        ?string $special = null,
        ?string $main = null,
        ?string $canonical = null,
        ?bool $salmon = null,
        array $aliases = [],
        bool $enableAutoKey = true,
        ?string $xGroup = null,
        ?string $xGroup2 = null,
        ?string $releaseAt = null,
    ): void {
        if ($salmon === null) {
            $salmon = $main === null;
        }

        $weaponId = $this->upWeapon3Impl(
            key: $key,
            name: $name,
            sub: $sub,
            special: $special,
            canonical: $canonical,
            mainWeaponId: $main === null
                ? $this->upMainWeapon3($key, $type, $name)
                : $this->key2id('{{%mainweapon3}}', $main),
            releaseAt: $releaseAt ?? '2022-01-01T00:00:00+00:00',
        );

        if ($enableAutoKey) {
            $autoKey = self::name2key3($name);
            if ($key !== $autoKey && !in_array($autoKey, $aliases, true)) {
                $aliases[] = $autoKey;
            }
        }

        // up salmon_weapon3 if needed
        $salmonWeaponId = null;
        if ($salmon) {
            if ($this->hasSalmonWeapon3Rank()) {
                $this->insert('{{%salmon_weapon3}}', [
                    'key' => $key,
                    'name' => $name,
                    'weapon_id' => $weaponId,
                    'rank' => array_reduce(
                        $aliases,
                        function (?int $carry, string $alias): ?int {
                            if ($carry !== null) {
                                return $carry;
                            }

                            if (preg_match('/^\d+$/', $alias)) {
                                return intval($alias, 10);
                            }

                            return null;
                        },
                        null,
                    ),
                ]);
            } else {
                $this->insert('{{%salmon_weapon3}}', [
                    'key' => $key,
                    'name' => $name,
                    'weapon_id' => $weaponId,
                ]);
            }
            $salmonWeaponId = $this->key2id('{{%salmon_weapon3}}', $key);
        }

        // proc aliases for both weapon3 and salmon_weapon3
        foreach ($aliases as $alias) {
            $this->insert('{{%weapon3_alias}}', [
                'weapon_id' => $weaponId,
                'key' => $alias,
            ]);

            if ($salmon && $salmonWeaponId) {
                $this->insert('{{%salmon_weapon3_alias}}', [
                    'weapon_id' => $salmonWeaponId,
                    'key' => $alias,
                ]);
            }
        }

        if ($xGroup) {
            $this->insert('{{%x_matching_group_weapon3}}', [
                'version_id' => $this->key2id(
                    '{{%x_matching_group_version3}}',
                    '2.0.0',
                    keyColumn: 'minimum_version',
                ),
                'group_id' => $this->key2id(
                    '{{%x_matching_group3}}',
                    $xGroup,
                    keyColumn: 'short_name',
                ),
                'weapon_id' => $weaponId,
            ]);
        }

        if ($xGroup2) {
            $this->insert('{{%x_matching_group_weapon3}}', [
                'version_id' => $this->key2id(
                    '{{%x_matching_group_version3}}',
                    '6.0.0',
                    keyColumn: 'minimum_version',
                ),
                'group_id' => $this->key2id(
                    '{{%x_matching_group3}}',
                    $xGroup2,
                    keyColumn: 'short_name',
                ),
                'weapon_id' => $weaponId,
            ]);
        }
    }

    /**
     * @phpstan-param type3-string $type
     */
    private function upMainWeapon3(string $key, string $type, string $name): int
    {
        $this->insert('{{%mainweapon3}}', [
            'key' => $key,
            'type_id' => $this->key2id('{{%weapon_type3}}', $type),
            'name' => $name,
        ]);

        return $this->key2id('{{%mainweapon3}}', $key);
    }

    /**
     * @phpstan-param non-empty-string $key
     * @phpstan-param non-empty-string $name
     * @phpstan-param sub3-string|null $sub
     * @phpstan-param sp3-string|null $special
     * @phpstan-param non-empty-string|null $canonical
     */
    private function upWeapon3Impl(
        string $key,
        string $name,
        ?string $sub,
        ?string $special,
        int $mainWeaponId,
        ?string $canonical,
        string $releaseAt,
    ): int {
        $data = [
            'key' => $key,
            'mainweapon_id' => $mainWeaponId,
            'subweapon_id' => $sub === null ? null : $this->key2id('{{%subweapon3}}', $sub),
            'special_id' => $special === null ? null : $this->key2id('{{%special3}}', $special),
            'canonical_id' => $canonical === null
                ? new Expression("currval('weapon3_id_seq'::regclass)")
                : $this->key2id('{{%weapon3}}', $canonical),
            'name' => $name,
        ];
        if ($this->hasWeapon3ReleaseAt()) {
            $data['release_at'] = $releaseAt;
        }

        $this->insert('{{%weapon3}}', $data);
        return $this->key2id('{{%weapon3}}', $key);
    }

    /**
     * @phpstan-param non-empty-string $key
     */
    protected function downWeapon3(string $key, bool $salmon = true): void
    {
        if ($salmon) {
            $salmonWeaponId = $this->key2id('{{%salmon_weapon3}}', $key);
            $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $salmonWeaponId]);
            $this->delete('{{%salmon_weapon3}}', ['id' => $salmonWeaponId]);
        }

        $weaponId = $this->key2id('{{%weapon3}}', $key);
        $this->delete('{{%x_matching_group_weapon3}}', ['weapon_id' => $weaponId]);
        $this->delete('{{%weapon3_alias}}', ['weapon_id' => $weaponId]);
        $this->delete('{{%weapon3}}', ['id' => $weaponId]);

        $mainWeaponId = $this->key2id(
            '{{%mainweapon3}}',
            $key,
            nullable: true,
        );
        if ($mainWeaponId !== null) {
            $isUsed = (new Query())
                ->select('*')
                ->from('{{%weapon3}}')
                ->andWhere(['mainweapon_id' => $mainWeaponId])
                ->exists();
            if (!$isUsed) {
                $this->delete('{{%mainweapon3}}', ['id' => $mainWeaponId]);
            }
        }
    }

    private function hasWeapon3ReleaseAt(): bool
    {
        return $this->hasColumn('weapon3', 'release_at');
    }

    private function hasSalmonWeapon3Rank(): bool
    {
        return $this->hasColumn('salmon_weapon3', 'rank');
    }

    private function hasColumn(string $table, string $column): bool
    {
        return (bool)(new Query())
            ->select('*')
            ->from('{{information_schema}}.{{columns}}')
            ->andWhere([
                'table_name' => $table,
                'column_name' => $column,
            ])
            ->one();
    }
}
