<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\Spl2AbilityAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function array_merge_recursive;
use function sprintf;

class AbilityIcon extends Widget
{
    public $icon;
    public $asset;
    public $contentOptions;

    public static function spl2(string $icon, array $contentOptions = []): string
    {
        return static::widget([
            'icon' => $icon,
            'asset' => Spl2AbilityAsset::register(Yii::$app->view),
            'contentOptions' => $contentOptions,
        ]);
    }

    public function run()
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl(
                $this->asset,
                sprintf('%s.png', $this->icon),
            ),
            array_merge_recursive([
                'id' => $this->id,
                'class' => [
                    'ability',
                    'ability-' . $this->icon,
                ],
            ], $this->contentOptions),
        );
    }
}
