<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\ability\effect;

class v020800 extends v020700
{
    public function getCalculatorVersion()
    {
        return '2.8.0';
    }

    protected function getSpecialLossPctBase()
    {
        switch ($this->battle->weapon->key ?? null) {
            case 'barrelspinner_remix':
            case 'bold_7':
            case 'h3reelgun_cherry':
            case 'longblaster_necro':
            case 'nova':
            case 'splatroller_corocoro':
            case 'splatspinner_repair':
                return 0.4;

            case 'nzap83':
            case 'promodeler_pg':
            case 'splatcharger_bento':
            case 'splatscope_bento':
                return 0.6;

            case 'splatspinner_collabo':
                return 0.75;

            default:
                return parent::getSpecialLossPctBase();
        }
    }
}
