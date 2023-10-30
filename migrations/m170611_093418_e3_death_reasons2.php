<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170611_093418_e3_death_reasons2 extends Migration
{
    public function safeUp()
    {
        $this->registerWeapons();
        $this->registerSubweapons();
        $this->registerSpecials();
    }

    public function safeDown()
    {
        $this->unregisterSpecials();
        $this->unregisterSubweapons();
        $this->unregisterWeapons();
    }

    private function registerWeapons(): void
    {
        $this->register(
            (int)(new Query())->select('id')->from('death_reason_type2')->where(['key' => 'main'])->scalar(),
            'weapon2',
            [
                'barrelspinner',
                'bucketslosher',
                'dynamo',
                'hokusai',
                'hotblaster',
                'prime',
                'sharp',
                'splatscope',
            ],
            'weapon_id',
        );
    }

    private function unregisterWeapons(): void
    {
        $this->unregister([
            'barrelspinner',
            'bucketslosher',
            'dynamo',
            'hokusai',
            'hotblaster',
            'prime',
            'sharp',
            'splatscope',
        ]);
    }

    private function registerSubweapons(): void
    {
        $this->register(
            (int)(new Query())->select('id')->from('death_reason_type2')->where(['key' => 'sub'])->scalar(),
            'subweapon2',
            [
                'rocketbomb',
                'splashshield',
                'sprinkler',
                'trap',
            ],
            'subweapon_id',
        );
    }

    private function unregisterSubweapons(): void
    {
        $this->unregister([
            'rocketbomb',
            'splashshield',
            'sprinkler',
            'trap',
        ]);
    }

    private function registerSpecials(): void
    {
        $this->register(
            (int)(new Query())->select('id')->from('death_reason_type2')->where(['key' => 'special'])->scalar(),
            'special2',
            ['amefurashi'],
            'special_id',
        );
    }

    private function unregisterSpecials(): void
    {
        $this->unregister(['amefurashi']);
    }

    private function register(int $typeId, string $table, array $keys, string $idCol): void
    {
        $this->batchInsert(
            'death_reason2',
            [
                'key',
                'type_id',
                $idCol,
                'name',
            ],
            array_map(
                fn (array $row): array => [
                    $row['key'],
                    $typeId,
                    $row['id'],
                    $row['name'],
                ],
                (new Query())
                    ->select(['key', 'id', 'name'])
                    ->from($table)
                    ->where(['key' => $keys])
                    ->orderBy('id ASC')
                    ->all(),
            ),
        );
    }

    private function unregister(array $key): void
    {
        $this->delete('death_reason2', ['key' => $key]);
    }
}
