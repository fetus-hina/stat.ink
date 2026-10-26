<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\ability\effect;

use Override;

class v020900 extends v020800
{
    #[Override]
    public function getCalculatorVersion(): string
    {
        return '2.9.0';
    }

    #[Override]
    protected function getSpecialDurationDefaultSec(): int|float|null
    {
        switch ($this->battle->weapon->special->key ?? null) {
            case 'barrier':
                return 4.5;

            case 'daioika':
                return 5;

            default:
                return parent::getSpecialDurationDefaultSec();
        }
    }

    #[Override]
    protected function getSpecialDurationUpPattern(): int
    {
        switch ($this->battle->weapon->special->key ?? null) {
            case 'barrier':
            case 'daioika':
                return static::SPECIAL_DURATION_60PCT;

            default:
                return parent::getSpecialDurationUpPattern();
        }
    }
}
