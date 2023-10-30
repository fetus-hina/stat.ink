<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170507_103656_death_reason_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'death_reason_type2',
            ['key', 'name'],
            [
                ['main', 'Main Weapon'],
                ['sub', 'Sub Weapon'],
                ['special', 'Special Weapon'],
                ['oob', 'Out of Bounds'],
                ['gadget', 'Gadgets'],
            ],
        );

        $types = $this->getReasonTypes();

        $this->insert('death_reason2', [
            'key' => 'unknown',
            'name' => 'Unknown',
        ]);
        $this->batchInsert(
            'death_reason2',
            ['type_id', 'key', 'name'],
            [
                [$types['oob'], 'fall', 'Fall'],
                [$types['oob'], 'drown', 'Drowning'],
                [$types['oob'], 'oob', 'Out of Bounds'],
            ],
        );
        $this->batchInsert(
            'death_reason2',
            ['type_id', 'weapon_id', 'key', 'name'],
            array_map(
                fn ($row) => [
                    (int)$types['main'],
                    (int)$row['id'],
                    $row['key'],
                    $row['name'],
                ],
                (new Query())
                    ->select(['id', 'key', 'name'])
                    ->from('weapon2')
                    ->orderBy('id')
                    ->all(),
            ),
        );
        // この時点のサブウェポンは全部殺せる
        $this->batchInsert(
            'death_reason2',
            ['type_id', 'subweapon_id', 'key', 'name'],
            array_map(
                fn ($row) => [
                    (int)$types['sub'],
                    (int)$row['id'],
                    $row['key'],
                    $row['name'],
                ],
                (new Query())
                    ->select(['id', 'key', 'name'])
                    ->from('subweapon2')
                    ->orderBy('id')
                    ->all(),
            ),
        );
        // この時点のスペシャルウェポンは全部殺せる
        $this->batchInsert(
            'death_reason2',
            ['type_id', 'special_id', 'key', 'name'],
            array_map(
                fn ($row) => [
                    (int)$types['special'],
                    (int)$row['id'],
                    $row['key'],
                    $row['name'],
                ],
                (new Query())
                    ->select(['id', 'key', 'name'])
                    ->from('special2')
                    ->orderBy('id')
                    ->all(),
            ),
        );
    }

    public function safeDown()
    {
        $this->delete('death_reason2');
        $this->delete('death_reason_type2');
    }

    public function getReasonTypes(): array
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('death_reason_type2')
                ->all(),
            'key',
            'id',
        );
    }
}
