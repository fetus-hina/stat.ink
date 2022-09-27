<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\weaponIcon;

use Yii;
use app\models\Special3;

final class SpecialIcon extends BaseWeaponIcon
{
    public ?Special3 $model = null;

    protected function getType(): string
    {
        return 'special';
    }

    protected function getKey(): ?string
    {
        return $this->model ? $this->model->key : null;
    }

    protected function getAlt(): ?string
    {
        return $this->model ? Yii::t('app-special3', $this->model->name) : null;
    }
}
