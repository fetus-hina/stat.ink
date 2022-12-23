<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

trait Weapon3Trait
{
    use weapon3\SpecialUseTrait;

    protected function updateEntireWeapons3(): void
    {
        $this->makeStatWeapon3SpecialUse();
    }
}
