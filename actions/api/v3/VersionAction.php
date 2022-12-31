<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\SplatoonVersionGroupApiFormatter;
use app\models\SplatoonVersionGroup3;
use yii\web\ViewAction;

final class VersionAction extends ViewAction
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
    public function run(bool $full = false): array
    {
        $groups = SplatoonVersionGroup3::find()
            ->with(['splatoonVersion3s'])
            ->andWhere(['not', ['tag' => '0.0']])
            ->all();

        \usort(
            $groups,
            fn (SplatoonVersionGroup3 $a, SplatoonVersionGroup3 $b): int => \version_compare($b->tag, $a->tag)
                ?: \strcmp($b->tag, $a->tag)
                ?: $b->id <=> $a->id
        );

        return \array_map(
            fn (SplatoonVersionGroup3 $group): array => SplatoonVersionGroupApiFormatter::toJson($group, $full, true),
            $groups,
        );
    }
}
