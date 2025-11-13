<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\UnregisteredPlayerApiFormatter;
use app\models\UnregisteredPlayer3;
use yii\base\Action;
use yii\web\NotFoundHttpException;

final class UnregisteredPlayerAction extends Action
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

    public function run(string $splashtag, bool $full = false): array
    {
        $player = UnregisteredPlayer3::findBySplashtagString($splashtag);

        return UnregisteredPlayerApiFormatter::toJson($player, $full) ?? throw new NotFoundHttpException('Unregistered player not found.');
    }
}