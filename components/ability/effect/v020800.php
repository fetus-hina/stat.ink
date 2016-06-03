<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\ability\effect;

class v020800 extends v020700
{
    protected function getSpecialLossPctBase()
    {
        switch ($this->battle->weapon->key ?? null) {
            case 'nova':
            case 'splatspinner_repair':
                return 0.4;

            case 'splatspinner_collabo':
                return 0.75;

            default:
                return parent::getSpecialLossPctBase();
        }
    }
}
