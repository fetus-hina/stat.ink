<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use Yii;
use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Map3;
use app\models\Rule3;
use app\models\RuleGroup3;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait QueryDecoratorTrait
{
    public function decorateQuery(ActiveQuery $query): void
    {
        if ($this->hasErrors()) {
            Yii::warning('This form has errors', __METHOD__);
            $query->andWhere('1 <> 1'); // make no results
            return;
        }

        $this->decorateGroupFilter(
            $query,
            '{{%battle3}}.[[lobby_id]]',
            $this->lobby,
            Lobby3::class,
            LobbyGroup3::class,
            '{{%lobby3}}.[[group_id]]',
        );

        $this->decorateGroupFilter(
            $query,
            '{{%battle3}}.[[rule_id]]',
            $this->rule,
            Rule3::class,
            RuleGroup3::class,
            '{{%rule3}}.[[group_id]]',
        );

        $this->decorateSimpleFilter($query, '{{%battle3}}.[[map_id]]', $this->map, Map3::class);
    }

    /**
     * @phpstan-param class-string<ActiveRecord> $modelClass
     * @phpstan-param class-string<ActiveRecord> $groupClass
     */
    private function decorateGroupFilter(
        ActiveQuery $query,
        string $column,
        ?string $key,
        string $modelClass,
        string $groupClass,
        string $groupAttr // group_id
    ): void {
        $key = \trim((string)$key);
        if ($key !== '') {
            if (!\str_starts_with($key, '@')) {
                // NOT group
                $this->decorateSimpleFilter($query, $column, $key, $modelClass);
                return;
            }


            if (!$groupId = self::findIdByKey($groupClass, \substr($key, 1))) {
                $query->andWhere('1 <> 1');
                return;
            }

            $query->andWhere([
                $column => ArrayHelper::getColumn(
                    $modelClass::find()
                        ->andWhere([$groupAttr => $groupId])
                        ->all(),
                    'id',
                ),
            ]);
        }
    }

    /**
     * @phpstan-param class-string<ActiveRecord> $modelClass
     */
    private function decorateSimpleFilter(
        ActiveQuery $query,
        string $column,
        ?string $key,
        string $modelClass
    ): void {
        $key = \trim((string)$key);
        if ($key !== '') {
            $query->andWhere([
                $column => self::findIdByKey($modelClass, $key),
            ]);
        }
    }

    /**
     * @phpstan-param class-string<ActiveRecord> $modelClass
     */
    private static function findIdByKey(
        string $modelClass,
        string $key,
        string $column = 'key'
    ): ?int {
        $model = $modelClass::find()
            ->andWhere([$column => $key])
            ->limit(1)
            ->one();
        return $model ? (int)$model->id : null;
    }
}
