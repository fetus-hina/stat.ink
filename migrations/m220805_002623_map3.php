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

class m220805_002623_map3 extends Migration
{
    use AutoKey;

    private const S3_LAUNCH = '2022-09-09T00:00:00+09:00';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%map3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'name' => $this->string(48)->notNull()->unique(),
            'short_name' => $this->string(16)->notNull()->unique(),
            'area' => $this->integer()->null(),
            'release_at' => $this->timestampTZ()->null(),
        ]);

        $this->createTable('{{%map3_alias}}', [
            'id' => $this->primaryKey(),
            'map_id' => $this->pkRef('{{%map3}}'),
            'key' => $this->apiKey3(),
            'UNIQUE ([[map_id]], [[key]])',
        ]);

        $allData = [
            'yunohana' => ['Scorch Gorge', 'Gorge'],
            'gonzui' => ['Eeltail Alley', 'Alley'],
            'kinmedai' => ["Museum d'Alfonsino", 'Museum'],
            'mategai' => ['Undertow Spillway', 'Spillway'],
            'namero' => ['Mincemeat Metalworks', 'Metalworks'],
        ];

        $this->batchInsert(
            '{{%map3}}',
            ['key', 'name', 'short_name', 'release_at'],
            array_map(
                function (string $key, array $names): array {
                    return [$key, $names[0], $names[1], self::S3_LAUNCH];
                },
                array_keys($allData),
                array_values($allData),
            ),
        );

        $ids = ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('{{%map3}}')->all(),
            'key',
            'id',
        );
        $aliases = [];
        foreach ($allData as $key => $names) {
            $aliases[] = [self::name2key3($names[1]), $ids[$key]];
            $aliases[] = [self::name2key3($names[0]), $ids[$key]];
        }
        $this->batchInsert('{{%map3_alias}}', ['key', 'map_id'], $aliases);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%map3_alias}}',
            '{{%map3}}',
        ]);

        return true;
    }
}
