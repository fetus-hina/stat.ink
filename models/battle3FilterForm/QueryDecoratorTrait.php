<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use Yii;
use app\models\Map3;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

trait QueryDecoratorTrait
{
    public function decorateQuery(ActiveQuery $query): void
    {
        if ($this->hasErrors()) {
            Yii::warning('This form has errors', __METHOD__);
            $query->andWhere('1 <> 1'); // make no results
            return;
        }

        $this->decorateSimpleFilter($query, '{{%battle3}}.[[map_id]]', $this->map, Map3::class);
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
