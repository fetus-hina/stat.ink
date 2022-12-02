<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\SalmonWeapon3;
use app\models\Weapon3;
use yii\base\Widget;
use yii\helpers\Html;

final class WeaponName extends Widget
{
    public Weapon3|SalmonWeapon3|null $model = null;
    public bool $showName = true;
    public bool $subInfo = true;

    public function run(): string
    {
        $model = $this->model;
        if (!$model) {
            return '';
        }

        return $this->subInfo
            ? \vsprintf('%s (%s)', [
                $this->renderMainWeapon($model),
                $this->renderSubSp($model),
            ])
            : $this->renderMainWeapon($model);
    }

    private function renderMainWeapon(Weapon3|SalmonWeapon3 $model): string
    {
        if ($this->showName) {
            return \implode(' ', [
                WeaponIcon::widget(['model' => $model, 'alt' => false]),
                Html::encode(Yii::t('app-weapon3', $model->name)),
            ]);
        }

        return WeaponIcon::widget(['model' => $model]);
    }

    private function renderSubSp(Weapon3|SalmonWeapon3 $model): string
    {
        if (!$this->subInfo || !$model instanceof Weapon3) {
            return '';
        }

        $sub = $model->subweapon;
        $sp = $model->special;
        if (!$sub || !$sp) {
            return '';
        }

        return $this->showName
            ? \vsprintf('%s / %s', [
                \implode(' ', [
                    SubweaponIcon::widget(['model' => $sub, 'alt' => false]),
                    Html::encode(Yii::t('app-subweapon3', $sub->name)),
                ]),
                \implode(' ', [
                    SpecialIcon::widget(['model' => $sp, 'alt' => false]),
                    Html::encode(Yii::t('app-special3', $sp->name)),
                ]),
            ])
            : \implode(' ', [
                SubweaponIcon::widget(['model' => $sub]),
                SpecialIcon::widget(['model' => $sp]),
            ]);
    }
}
