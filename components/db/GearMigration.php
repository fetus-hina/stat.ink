<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use yii\db\Query;

trait GearMigration
{
    use AutoKey;

    protected function upGear2(
        string $key,
        string $name,
        string $type,
        string $brand,
        ?string $ability,
        ?int $splatnet,
    ): void {
        $this->insert('gear2', [
            'key' => $key,
            'name' => $name,
            'type_id' => $this->findId('gear_type', $type),
            'brand_id' => $this->findId('brand2', $brand),
            'ability_id' => $ability ? $this->findId('ability2', $ability) : null,
            'splatnet' => $splatnet,
        ]);
    }

    protected function downGear2(string $key): void
    {
        $this->delete('gear2', ['key' => $key]);
    }

    protected static function salmonGear2(string $name, string $type, ?int $splatnet): array
    {
        return [
            static::name2key($name),
            $name,
            $type,
            'grizzco',
            null,
            $splatnet,
        ];
    }

    private function findId(string $table, string $tag): ?int
    {
        $id = (new Query())
            ->select('id')
            ->from($table)
            ->where(['key' => $tag])
            ->limit(1)
            ->scalar();
        return $id ? (int)$id : null;
    }
}
