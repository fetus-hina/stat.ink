<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\battleDelete;

use Yii;
use app\components\widgets\Icon;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

final class Button extends Widget
{
    public string $modalSelector = '#modal';

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            BootstrapAsset::register($view);
            BootstrapPluginAsset::register($view);
        }

        return Html::button(
            Icon::delete(),
            [
                'class' => [
                    'auto-tooltip',
                    'btn',
                    'btn-link',
                    'btn-xs',
                ],
                'data' => [
                    'target' => $this->modalSelector,
                    'toggle' => 'modal',
                ],
                'title' => Yii::t('app', 'Delete'),
            ],
        );
    }
}
