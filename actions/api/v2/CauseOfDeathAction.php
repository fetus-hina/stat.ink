<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2;

use Generator;
use TypeError;
use Yii;
use app\models\DeathReason2;
use yii\base\Action;
use yii\db\Connection;
use yii\web\Response;

use function array_keys;
use function array_map;
use function array_values;
use function assert;
use function implode;
use function vsprintf;

use const SORT_ASC;

final class CauseOfDeathAction extends Action
{
    public const FORMAT_DEFAULT = '';
    public const FORMAT_REDUCED = 'reduced';
    public const FORMAT_IKALOG = 'ikalog';

    public function run(string $format = self::FORMAT_DEFAULT): Response
    {
        if ($format !== self::FORMAT_REDUCED && $format !== self::FORMAT_IKALOG) {
            $format = self::FORMAT_DEFAULT;
        }

        $response = Yii::$app->response;
        assert($response instanceof Response);
        $response->format = 'json';

        $json = [];
        foreach ($this->getAll() as $model) {
            $json[$model->key] = $this->toJson($model, $format);
        }
        $response->data = $json;

        return $response;
    }

    /**
     * @phpstan-return Generator<DeathReason2>
     */
    private function getAll(): Generator
    {
        $typeOrderList = [
            'main',
            'sub',
            'special',
            'hoko',
            'gadget',
            'oob',
        ];

        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            throw new TypeError();
        }
        $typeOrder = vsprintf('(CASE %s ELSE %d END)', [
            implode(' ', array_map(
                fn (int $index, string $typeKey): string => vsprintf('WHEN %s.%s = %s THEN %d', [
                    $db->quoteTableName('death_reason_type2'),
                    $db->quoteColumnName('key'),
                    $db->quoteValue($typeKey),
                    $index + 1,
                ]),
                array_keys($typeOrderList),
                array_values($typeOrderList),
            )),
            0x7fffffff,
        ]);

        $query = DeathReason2::find()
            ->joinWith(['type'], true)
            ->with([
                'special',
                'subweapon',
                'weapon',
            ])
            ->orderBy([
                $typeOrder => SORT_ASC,
                'key' => SORT_ASC,
            ]);
        foreach ($query->each() as $model) {
            yield $model;
        }
    }

    /**
     * @param self::FORMAT_DEFAULT|self::FORMAT_REDUCED|self::FORMAT_IKALOG $format
     */
    private function toJson(DeathReason2 $model, string $format): array
    {
        switch ($format) {
            case self::FORMAT_REDUCED:
                return $this->toReducedJson($model);

            case self::FORMAT_IKALOG:
                return $this->toIkalogJson($model);
        }

        return $this->toDefaultJson($model);
    }

    private function toDefaultJson(DeathReason2 $model): array
    {
        $results = [
            'key' => $model->key,
            'type' => null,
            'related_to' => null,
            'name' => $model->getTranslatedNameList(),
        ];

        if ($model->type) {
            $results['type'] = $model->type->key;
            switch ($model->type->key) {
                case 'main':
                    if ($model->weapon) {
                        $results['related_to'] = $model->weapon->key;
                    }
                    break;

                case 'sub':
                    if ($model->subweapon) {
                        $results['related_to'] = $model->subweapon->key;
                    }
                    break;

                case 'special':
                    if ($model->special) {
                        $results['related_to'] = $model->special->key;
                    }
                    break;
            }
        }

        return $results;
    }

    private function toReducedJson(DeathReason2 $model): array
    {
        return [
            'key' => $model->key,
            'name' => [
                'ja_JP' => $model->getTranslatedName('ja-JP'),
                'en_US' => $model->name,
            ],
        ];
    }

    private function toIkalogJson(DeathReason2 $model): array
    {
        return [
            'ja' => $model->getTranslatedName('ja-JP'),
            'en' => $model->name,
        ];
    }
}
