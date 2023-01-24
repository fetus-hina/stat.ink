<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use app\commands\stat\weapon3\PerMetricsUpdator;
use app\commands\stat\weapon3\SpecialUseCountTrait;
use app\commands\stat\weapon3\SpecialUseTrait;
use app\commands\stat\weapon3\WeaponUsageTrait;

trait Weapon3Trait
{
    use SpecialUseCountTrait;
    use SpecialUseTrait;
    use WeaponUsageTrait;

    protected function updateEntireWeapons3(): void
    {
        $this->makeStatWeapon3Usage();
        PerMetricsUpdator::update();
        $this->makeStatWeapon3SpecialUse();
        $this->makeStatWeapon3SpecialUseCount();
    }
}
