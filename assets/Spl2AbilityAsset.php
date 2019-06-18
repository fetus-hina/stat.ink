<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class Spl2AbilityAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/abilities/spl2';

    public function getIconUrl(string $key): string
    {
        return Yii::$app->assetManager->getAssetUrl($this, "{$key}.png");
    }
}
