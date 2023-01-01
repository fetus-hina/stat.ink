<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

use function sprintf;

final class GameVersionIconAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/game-version-icon';

    public function getIconUrl(int $version): string
    {
        return Yii::$app->assetManager->getAssetUrl($this, sprintf('s%d.png', $version));
    }
}
