<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm\dropdownList;

use Yii;
use app\models\Battle3PlayedWith;
use app\models\User;
use yii\helpers\ArrayHelper;

use function array_merge;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;

trait PlayedWithDropdownListTrait
{
    public function getPlayedWithDropdown(?User $user, ?Battle3PlayedWith $currentFilter): array
    {
        return [
            array_merge(
                $currentFilter
                    ? [$currentFilter->ref_id => self::formatPlayedWithName($currentFilter)]
                    : [],
                $this->getFrequentlyPlayedWithDropdown($user, $currentFilter),
            ),
            ['prompt' => Yii::t('app', 'Played With')],
        ];
    }

    private function getFrequentlyPlayedWithDropdown(
        ?User $user,
        ?Battle3PlayedWith $exclude,
    ): array {
        if (!$user) {
            return [];
        }

        return ArrayHelper::map(
            Battle3PlayedWith::find()
                ->andWhere(['and',
                    ['user_id' => $user->id],
                    ['>', 'count', 1],
                    $exclude
                        ? ['<>', 'ref_id', $exclude->ref_id]
                        : '1 = 1',
                ])
                ->orderBy([
                    'count' => SORT_DESC,
                    'name' => SORT_ASC,
                    'number' => SORT_ASC,
                ])
                ->limit(20)
                ->all(),
            'ref_id',
            self::formatPlayedWithName(...),
        );
    }

    private static function formatPlayedWithName(Battle3PlayedWith $model): string
    {
        return vsprintf('%s #%s (×%s)', [
            $model->name,
            $model->number,
            Yii::$app->formatter->asInteger($model->count),
        ]);
    }
}
