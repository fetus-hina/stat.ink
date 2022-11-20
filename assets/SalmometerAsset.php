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

final class SalmometerAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/salmometer';

    /**
     * @phpstan-type int<0, 5> $level
     * @phpstan-type 'yokozuna' $salmonid
     */
    public function getIconUrl(int $level, string $salmonid = 'yokozuna'): string
    {
        return Yii::$app->assetManager->getAssetUrl(
            $this,
            "{$salmonid}/{$level}.png"
        );
    }
}
