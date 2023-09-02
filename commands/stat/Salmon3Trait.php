<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use app\commands\stat\salmon3\SalmometerTrait;
use app\commands\stat\salmon3\TideEventTrait;

trait Salmon3Trait
{
    use SalmometerTrait;
    use TideEventTrait;

    protected function updateEntireSalmon3(): void
    {
        $this->makeStatSalmon3TideEvent();
        $this->makeStatSalmon3Salmometer();
    }
}
