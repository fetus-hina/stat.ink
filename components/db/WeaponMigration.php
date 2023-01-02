<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use Exception;
use yii\db\Expression;
use yii\db\Query;

use function array_filter;

trait WeaponMigration
{
    protected function upWeapon(
        string $key,
        string $name,
        string $type,
        string $sub,
        string $special,
        ?string $main = null,
        ?string $canonical = null,
        ?int $splatnet = null
    ): void {
        $this->db->transaction(function () use (
            $key,
            $name,
            $type,
            $sub,
            $special,
            $main,
            $canonical,
            $splatnet
        ): void {
            $this->insert('weapon2', array_filter([
                'key' => $key,
                'name' => $name,
                'type_id' => $this->findId('weapon_type2', $type),
                'subweapon_id' => $this->findId('subweapon2', $sub),
                'special_id' => $this->findId('special2', $special),
                'main_group_id' => $main !== null
                    ? $this->findId('weapon2', $main)
                    : new Expression("currval('weapon2_id_seq'::regclass)"),
                'canonical_id' => $canonical !== null
                    ? $this->findId('weapon2', $canonical)
                    : new Expression("currval('weapon2_id_seq'::regclass)"),
                'splatnet' => $this->splatnetId($splatnet, $main),
            ]));

            $this->insert('death_reason2', [
                'key' => $key,
                'name' => $name,
                'type_id' => $this->findId('death_reason_type2', 'main'),
                'weapon_id' => $this->findId('weapon2', $key),
            ]);

            if ($main === null && $this->isTableExists('salmon_main_weapon2')) {
                $this->insert('salmon_main_weapon2', [
                    'key' => $key,
                    'name' => $name,
                    'splatnet' => $splatnet,
                    'weapon_id' => $this->findId('weapon2', $key),
                ]);
            }
        });
    }

    protected function downWeapon(string $key): void
    {
        $this->db->transaction(function () use ($key): void {
            if ($this->isTableExists('salmon_main_weapon2')) {
                $this->delete('salmon_main_weapon2', ['key' => $key]);
            }
            $this->delete('death_reason2', ['key' => $key]);
            $this->delete('weapon2', ['key' => $key]);
        });
    }

    protected function findWeaponId(string $key): int
    {
        return $this->findId('weapon2', $key);
    }

    // if $splatnet is negative, returns ($main->splatnet + abs $splatnet)
    private function splatnetId(?int $splatnet, ?string $main): ?int
    {
        if ($splatnet === null || $splatnet >= 0 || $main === null) {
            return $splatnet;
        }

        $parentId = (new Query())
            ->select('splatnet')
            ->from('weapon2')
            ->where(['key' => $main])
            ->limit(1)
            ->scalar();
        if ($parentId === null) {
            throw new Exception("Could not found {$main} in weapon2");
        }
        return ((int)$parentId) + -1 * $splatnet;
    }

    private function findId(string $table, string $key): int
    {
        $id = (new Query())
            ->select('id')
            ->from($table)
            ->where(['key' => $key])
            ->limit(1)
            ->scalar();
        if (!$id) {
            throw new Exception("Could not found {$key} in {$table}");
        }

        return (int)$id;
    }
}
