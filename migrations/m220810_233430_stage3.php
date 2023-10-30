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

final class m220810_233430_stage3 extends Migration
{
    use AutoKey;

    private const S3_LAUNCH = '2022-09-09T00:00:00+09:00';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $data = $this->getData();
        $this->batchInsert(
            '{{%map3}}',
            ['key', 'name', 'short_name', 'release_at'],
            array_map(
                fn (string $key, array $names): array => [
                    $key,
                    $names[0],
                    $names[1],
                    self::S3_LAUNCH,
                ],
                array_keys($data),
                array_values($data),
            ),
        );

        $mapIds = $this->getMapIds();
        $aliases = [];
        foreach ($this->getData() as $key => $names) {
            $mapId = (int)$mapIds[$key];
            $tmp = array_merge(
                $names[2] ?? [],
                [
                    self::name2key3($names[0]),
                    self::name2key3($names[1]),
                ],
            );
            sort($tmp, SORT_REGULAR);
            $aliases = array_merge($aliases, array_map(
                fn (string $alias): array => [$mapId, $alias],
                array_values(array_unique($tmp)),
            ));
        }
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], $aliases);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $ids = $this->getMapIds();
        foreach (array_keys($this->getData()) as $k) {
            $mapId = $ids[$k];
            $this->delete('{{%map3_alias}}', ['map_id' => $mapId]);
            $this->delete('{{%map3}}', ['id' => $mapId]);
        }

        return true;
    }

    /**
     * @return array<string, array{string, string, string[]|null}>
     */
    private function getData(): array
    {
        return [
            'yagara' => ['Hagglefish Market', 'Market'],
            'masaba' => ['Hammerhead Bridge', 'Bridge'],
            'mahimahi' => ['Mahi-Mahi Resort', 'Resort'],
            'zatou' => ['MakoMart', 'Mart'],
            'chozame' => ['Sturgeon Shipyard', 'Shipyard'],
            'amabi' => ['Inkblot Art Academy', 'Academy', ['ama']],
            'sumeshi' => ['Wahoo World', 'World'],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getMapIds(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('{{%map3}}')->all(),
            'key',
            'id',
        );
    }
}
