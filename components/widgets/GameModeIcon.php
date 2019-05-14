<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\GameModeIconsAsset;
use yii\base\Widget;
use yii\helpers\Html;

class GameModeIcon extends Widget
{
    public $icon;
    public $path;

    public static function spl2(string $icon): string
    {
        return static::widget([
            'icon' => $icon,
            'path' => "spl2/{$icon}.png"
        ]);
    }

    public function run()
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl(
                GameModeIconsAsset::register($this->view),
                $this->path
            ),
            [
                'id' => $this->id,
                'class' => [
                    'game-mode',
                    'game-mode-' . $this->icon,
                ],
            ]
        );
    }
}
