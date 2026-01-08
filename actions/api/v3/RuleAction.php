<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\RuleApiFormatter;
use app\models\Rule3;
use yii\base\Action;

use function array_map;

use const SORT_ASC;

final class RuleAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    /**
     * @return array[]
     */
    public function run(bool $full = false)
    {
        return array_map(
            fn (Rule3 $model): array => RuleApiFormatter::toJson($model, $full),
            Rule3::find()
                ->with(['rule3Aliases'])
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
        );
    }
}
