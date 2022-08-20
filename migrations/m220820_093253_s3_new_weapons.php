<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m220820_093253_s3_new_weapons extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upMainWeapon();
        $this->upWeapon();
        $this->upAlias();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%weapon3_alias}}', [
            'key' => ArrayHelper::getColumn(
                $this->getWeaponData(),
                function (array $data): string {
                    return self::name2key3($data['name']);
                },
            ),
        ]);

        $this->delete('{{%weapon3}}', [
            'key' => ArrayHelper::getColumn($this->getWeaponData(), 'key'),
        ]);

        $this->delete('{{%mainweapon3}}', [
            'key' => ArrayHelper::getColumn($this->getWeaponData(), 'key'),
        ]);

        return true;
    }

    private function upMainWeapon(): void
    {
        /**
         * @var array<string, int|numeric-string>
         */
        $types = ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('{{%weapon_type3}}')->all(),
            'key',
            'id',
        );

        $this->batchInsert('{{%mainweapon3}}', ['key', 'type_id', 'name'], array_map(
            function (array $item) use ($types): array {
                return [
                    $item['key'],
                    (int)$types[$item['type']],
                    $item['name'],
                ];
            },
            $this->getWeaponData(),
        ));
    }

    private function upWeapon(): void
    {
        /**
         * @var array<string, int|numeric-string>
         */
        $mains = ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('{{%mainweapon3}}')
                ->andWhere([
                    'key' => ArrayHelper::getColumn($this->getWeaponData(), 'key'),
                ])
                ->all(),
            'key',
            'id',
        );

        $this->batchInsert('{{%weapon3}}', ['key', 'mainweapon_id', 'name'], array_map(
            function (array $item) use ($mains): array {
                return [
                    $item['key'],
                    $mains[$item['key']],
                    $item['name'],
                ];
            },
            $this->getWeaponData(),
        ));
    }

    private function upAlias(): void
    {
        /**
         * @var array<string, int|numeric-string>
         */
        $weapons = ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('{{%weapon3}}')
                ->andWhere([
                    'key' => ArrayHelper::getColumn($this->getWeaponData(), 'key'),
                ])
                ->all(),
            'key',
            'id',
        );

        $this->batchInsert('{{%weapon3_alias}}', ['weapon_id', 'key'], array_map(
            function (array $item) use ($weapons): array {
                return [
                    $weapons[$item['key']],
                    self::name2key3($item['name']),
                ];
            },
            $this->getWeaponData(),
        ));
    }

    private function getWeaponData(): array
    {
        return [
            [
                'key' => 'tristringer',
                'type' => 'stringer',
                'name' => 'Tri-Stringer',
            ],
            [
                'key' => 'drivewiper',
                'type' => 'wiper',
                'name' => 'Splatana Wiper',
            ],
            [
                'key' => 'jimuwiper',
                'type' => 'wiper',
                'name' => 'Splatana Stamper',
            ],
        ];
    }
}
