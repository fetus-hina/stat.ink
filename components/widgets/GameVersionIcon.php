<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use app\assets\GameVersionIconAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

final class GameVersionIcon extends Widget
{
    public int $version = 1;

    public function run(): string
    {
        $view = $this->view;
        if (!$view instanceof View) {
            return '';
        }

        $asset = GameVersionIconAsset::register($view);
        return Html::img(
            $asset->getIconUrl($this->version),
            [
                'alt' => '',
                'draggable' => 'false',
                'style' => [
                    'height' => '1em',
                    'width' => 'auto',
                ],
                'title' => '',
            ],
        );
    }
}
