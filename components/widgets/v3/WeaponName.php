<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\models\Weapon3;
use yii\base\Widget;
use yii\bootstrap\Html;

final class WeaponName extends Widget
{
    public ?Weapon3 $model = null;
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

    private function renderMainWeapon(Weapon3 $model): string
    {
        //TODO: icon (respect showName)
        return Html::encode(Yii::t('app-weapon3', $model->name));
    }

    private function renderSubSp(Weapon3 $model): string
    {
        if (!$this->subInfo) {
            return '';
        }

        $sub = $model->subweapon;
        $sp = $model->special;
        if (!$sub || !$sp) {
            return '';
        }

        //TODO: icon (respect showName)
        // showNameでないときは "/" とかの区切りはたぶんいらない

        return \vsprintf('%s / %s', [
            Yii::t('app-subweapon3', $sub->name),
            Yii::t('app-special3', $sp->name),
        ]);
    }
}
