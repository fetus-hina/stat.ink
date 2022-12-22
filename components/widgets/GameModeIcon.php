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
use yii\base\Widget;
use yii\helpers\Html;

final class GameModeIcon extends Widget
{
    public string $icon;
    public string $path;
    public array $contentOptions = [];

    public static function spl2(string $icon, array $contentOptions = []): string
    {
        return static::widget([
            'icon' => $icon,
            'path' => "spl2/{$icon}.png",
            'contentOptions' => $contentOptions,
        ]);
    }

    public function run(): string
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl(
                GameModeIconsAsset::register($this->view),
                $this->path,
            ),
            array_merge_recursive(
                [
                    'class' => [
                        'game-mode',
                        'game-mode-' . $this->icon,
                    ],
                    'draggable' => 'false',
                    'id' => $this->id,
                ],
                (array)$this->contentOptions,
            ),
        );
    }
}
