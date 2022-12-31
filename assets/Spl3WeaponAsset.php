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

final class Spl3WeaponAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/weapons/spl3';

    /**
     * @phpstan-type 'main'|'sub'|'special' $type
     */
    public function getIconUrl(string $type, string $key): string
    {
        return Yii::$app->assetManager->getAssetUrl(
            $this,
            "{$type}/{$key}.png",
        );
    }
}
