<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\ability\effect;

class v020900 extends v020800
{
    protected function getSpecialDurationDefaultSec()
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

    protected function getSpecialDurationUpPattern()
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
