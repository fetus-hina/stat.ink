<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use Exception;
use app\models\Weapon2;
use yii\db\Query;

trait SalmonWeaponMigration
{
    protected function upSalmonWeapons2(array $keys): void
    {
        $this->db->transaction(function () use ($keys) {
            // check exists
            foreach ($keys as $key) {
                $query = Weapon2::find()->andWhere(['key' => $key]);
                if (!$query->exists()) {
                    throw new Exception("Weapon does not exists: {$key}");
                }
            }

            // copy weapons
            $select = (new Query())
                ->select([
                    'key',
                    'name',
                    'splatnet',
                    'weapon_id' => 'id',
                ])
                ->from('weapon2')
                ->andWhere(['key' => $keys]);
            $this->execute(
                'INSERT INTO {{salmon_main_weapon2}} ' .
                '([[key]], [[name]], [[splatnet]], [[weapon_id]]) ' .
                $select->createCommand()->rawSql,
            );
        });
    }

    protected function downSalmonWeapons2(array $keys): void
    {
        $this->delete('salmon_main_weapon2', [
            'key' => $keys,
        ]);
    }

    protected function upSalmonWeapon2(string $key): void
    {
        $this->upSalmonWeapons2([$key]);
    }

    protected function downSalmonWeapon2(string $key): void
    {
        $this->downSalmonWeapons2([$key]);
    }
}
