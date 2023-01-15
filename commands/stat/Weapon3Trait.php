<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use app\commands\stat\weapon3\SpecialUseCountTrait;
use app\commands\stat\weapon3\SpecialUseTrait;

trait Weapon3Trait
{
    use SpecialUseCountTrait;
    use SpecialUseTrait;

    protected function updateEntireWeapons3(): void
    {
        $this->makeStatWeapon3SpecialUse();
        $this->makeStatWeapon3SpecialUseCount();
    }
}
