<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\ability\effect;

class v020600 extends v020500
{
    public function getCalculatorVersion()
    {
        return '2.6.0';
    }

    public function getInkUsePctMain()
    {
        $x = $this->calcX('ink_saver_main', 200 / 3);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    public function getInkUsePctSub()
    {
        $x = $this->calcX('ink_saver_main', 600 / 7);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    public function getInkRecoverySec()
    {
        $x = $this->calcX('ink_recovery_up', 200 / 3);
        if ($x === null) {
            return null;
        }
        $f = 100 / (180 * (1 - $x));
        return ceil(100 / $f) / 60;
    }
}
