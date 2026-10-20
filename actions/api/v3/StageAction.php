<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use Override;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\StageApiFormatter;
use app\models\Map3;
use yii\base\Action;

use function array_map;

use const SORT_ASC;

final class StageAction extends Action
{
    use ApiInitializerTrait;

    #[Override]
    public function init(): void
    {
        parent::init();
        $this->apiInit();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function run(bool $full = false): array
    {
        return array_map(
            fn (Map3 $model): array => StageApiFormatter::toJson($model, $full),
            Map3::find()
                ->with(['map3Aliases'])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );
    }
}
