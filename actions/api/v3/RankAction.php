<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use Override;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\RankApiFormatter;
use app\models\Rank3;
use yii\base\Action;

use function array_map;

use const SORT_ASC;

final class RankAction extends Action
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
            fn (Rank3 $model): array => RankApiFormatter::toJson($model, $full),
            Rank3::find()
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
        );
    }
}
