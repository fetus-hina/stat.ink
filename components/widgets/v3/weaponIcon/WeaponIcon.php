<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\weaponIcon;

use Yii;
use app\models\Mainweapon3;
use app\models\SalmonWeapon3;
use app\models\Weapon3;

final class WeaponIcon extends BaseWeaponIcon
{
    /**
     * @var Weapon3|Mainweapon3|SalmonWeapon3|null
     */
    public $model = null;

    protected function getType(): string
    {
        return 'main';
    }

    protected function getKey(): ?string
    {
        return $this->model ? $this->model->key : null;
    }

    protected function getAlt(): ?string
    {
        return $this->model ? Yii::t('app-weapon3', $this->model->name) : null;
    }
}
