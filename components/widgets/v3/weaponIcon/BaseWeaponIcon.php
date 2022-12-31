<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\weaponIcon;

use app\assets\Spl3WeaponAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

abstract class BaseWeaponIcon extends Widget
{
    public bool $alt = true;

    abstract protected function getType(): string;
    abstract protected function getKey(): ?string;
    abstract protected function getAlt(): ?string;

    public function run(): string
    {
        $type = $this->getType();
        $key = $this->getKey();
        $view = $this->view;
        if (!$key || !$view instanceof View) {
            return '';
        }

        $asset = Spl3WeaponAsset::register($view);
        return Html::img(
            $asset->getIconUrl($type, $key),
            [
                'alt' => $this->alt ? $this->getAlt() : null,
                'class' => 'auto-tooltip basic-icon',
                'title' => $this->getAlt(),
            ],
        );
    }
}
