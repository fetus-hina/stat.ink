<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use yii\db\Expression;
use yii\db\Query;

/**
 * @phpstan-type type3-string1  'blaster'|'brella'|'brush'|'charger'|'maneuver'|'reelgun'|'roller'
 * @phpstan-type type3-string2  'shooter'|'slosher'|'spinner'|'stringer'|'wiper'
 * @phpstan-type sub3-string1   'curlingbomb'|'jumpbeacon'|'kyubanbomb'|'linemarker'|'pointsensor'
 * @phpstan-type sub3-string2   'poisonmist'|'quickbomb'|'robotbomb'|'splashbomb'|'splashshield'
 * @phpstan-type sub3-string3   'sprinkler'|'tansanbomb'|'torpedo'|'trap'
 * @phsptan-type sp3-string1    'amefurashi'|'energystand'|'greatbarrier'|'hopsonar'|'jetpack'
 * @phpstan-type sp3-string2    'kanitank'|'kyuinki'|'megaphone'|'missile'|'nicedama'|'sameride'
 * @phpstan-type sp3-string3    'shokuwander'|'tripletornado'|'ultrahanko'|'ultrashot'
 *
 * @phpstan-type type3-string   type3-string-1|type3-string-2
 * @phpstan-type sub3-string    sub3-string1|sub3-string2|sub3-string3
 * @phpstan-type sp3-string     sp3-string1|sp3-string2|sp3-string3
 */
trait Weapon3Migration
{
    use AutoKey;

    /**
     * @phpstan-param non-empty-string      $key
     * @phpstan-param non-empty-string      $name
     * @phpstan-param type3-string          $type
     * @phpstan-param sub3-string|null      $sub
     * @phpstan-param sp3-string|null       $special
     * @phpstan-param non-empty-string|null $main
     * @phpstan-param non-empty-string|null $canonical
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
        );

        if ($enableAutoKey) {
            $autoKey = self::name2key3($name);
            if ($key !== $autoKey && !\in_array($autoKey, $aliases, true)) {
                $aliases[] = $autoKey;
            }
        }

        // up salmon_weapon3 if needed
        $salmonWeaponId = null;
        if ($salmon) {
            $this->insert('{{%salmon_weapon3}}', [
                'key' => $key,
                'name' => $name,
                'weapon_id' => $weaponId,
            ]);
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
     * @phpstan-param non-empty-string      $key
     * @phpstan-param non-empty-string      $name
     * @phpstan-param sub3-string|null      $sub
     * @phpstan-param sp3-string|null       $special
     * @phpstan-param non-empty-string|null $canonical
     */
    private function upWeapon3Impl(
        string $key,
        string $name,
        ?string $sub,
        ?string $special,
        int $mainWeaponId,
        ?string $canonical,
    ): int {
        $this->insert('{{%weapon3}}', [
            'key' => $key,
            'mainweapon_id' => $mainWeaponId,
            'subweapon_id' => $sub === null ? null : $this->key2id('{{%subweapon3}}', $sub),
            'special_id' => $special === null ? null : $this->key2id('{{%special3}}', $special),
            'canonical_id' => $canonical === null
                ? new Expression("currval('weapon3_id_seq'::regclass)")
                : $this->key2id('{{%weapon3}}', $canonical),
            'name' => $name,
        ]);
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
        $this->delete('{{%weapon3_alias}}', ['weapon_id' => $weaponId]);
        $this->delete('{{%weapon3}}', ['id' => $weaponId]);

        $mainWeaponId = $this->key2id('{{%mainweapon3}}', $key);
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
