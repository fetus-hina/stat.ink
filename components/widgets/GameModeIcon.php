<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\GameModeIconsAsset;
use app\components\helpers\Html;
use yii\base\Widget;

class GameModeIcon extends Widget
{
    public $icon;
    public $path;
    public $contentOptions;

    public static function spl2(string $icon, array $contentOptions = []): string
    {
        return static::widget([
            'icon' => $icon,
            'path' => "spl2/{$icon}.png",
            'contentOptions' => $contentOptions,
        ]);
    }

    public function run()
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl(
                GameModeIconsAsset::register($this->view),
                $this->path
            ),
            array_merge_recursive([
                'id' => $this->id,
                'class' => [
                    'game-mode',
                    'game-mode-' . $this->icon,
                ],
            ], $this->contentOptions)
        );
    }
}
