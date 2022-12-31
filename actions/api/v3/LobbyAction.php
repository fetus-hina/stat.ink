<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\LobbyApiFormatter;
use app\models\Lobby3;
use yii\base\Action;

final class LobbyAction extends Action
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
        return \array_map(
            fn (Lobby3 $model): array => LobbyApiFormatter::toJson($model, $full),
            Lobby3::find()
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
        );
    }
}
