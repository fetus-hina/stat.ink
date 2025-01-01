<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\ability\effect;

use function ceil;
use function max;
use function round;

class v020500 extends Base
{
    public function getCalculatorVersion()
    {
        return '2.5.0';
    }

    public function getAttackPct()
    {
        $x = $this->calcX('damage_up', 100);
        if ($x === null) {
            return $x;
        }
        return 1 + $x;
    }

    public function getDefensePct()
    {
        $x = $this->calcX('defense_up', 100);
        if ($x === null) {
            return $x;
        }
        return 1 + $x;
    }

    public function getInkUsePctMain()
    {
        $x = $this->calcX('ink_saver_main', 75);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    public function getInkUsePctSub()
    {
        $x = $this->calcX('ink_saver_sub', 120);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    public function getInkRecoverySec()
    {
        $x = $this->calcX('ink_recovery_up', 75);
        if ($x === null) {
            return null;
        }
        $f = 100 / (180 * (1 - $x));
        return ceil(100 / $f) / 60;
    }

    public function getRunSpeedPct()
    {
        $x = $this->calcX('run_speed_up', 60);
        if ($x === null) {
            return null;
        }
        return 1 + $x;
    }

    public function getSwimSpeedPct()
    {
        $x = $this->calcX('swim_speed_up', 120);
        if ($x === null) {
            return null;
        }
        $ninja = $this->calcX('ninja_squid', 1);
        if ($ninja === null) {
            return null;
        }
        return (1 + $x) * ($ninja > 0 ? 0.9 : 1.0);
    }

    public function getSpecialChargePoint()
    {
        $x = $this->calcX('special_charge_up', 100);
        if ($x === null) {
            return null;
        }
        $defPoint = $this->getSpecialChargeDefaultPoint();
        if ($defPoint === null) {
            return null;
        }
        return (int)round($defPoint / (1 + $x));
    }

    public function getSpecialDurationSec()
    {
        $x = $this->calcX('special_duration_up', static::SPECIAL_DURATION_40PCT);
        if ($x === null) {
            return null;
        }
        $defSec = $this->getSpecialDurationDefaultSec();
        if ($defSec === null) {
            return null;
        }
        return (1 + $x) * $defSec;
    }

    public function getSpecialLossPct()
    {
        $x = $this->calcX('special_saver', 60);
        if ($x === null) {
            return null;
        }
        return max(0.5 - $x, 0);
    }

    public function getRespawnSec()
    {
        $x = $this->calcX('quick_respawn', 45);
        if ($x === null) {
            return null;
        }
        return ((1 - $x) * 360 + 30 + 120) / 60;
    }

    public function getSuperJumpSecs()
    {
        $x = $this->calcX('quick_super_jump', 60);
        if ($x === null) {
            return null;
        }
        return [
            'prepare' => 60 * (1 - $x) / 60,
            'pullup' => 120 * (1 - $x) / 60,
            'pulldown' => 40 / 60,
            'rigid' => 10 / 60,
        ];
    }

    public function getBombThrowPct()
    {
        $x = $this->calcX('bomb_range_up', 60);
        if ($x === null) {
            return null;
        }
        return 1 + $x;
    }

    public function getMarkingPct()
    {
        $x = $this->calcX('cold_blooded', 1);
        if ($x === null) {
            return null;
        }
        if ($x > 0) {
            return 0.25;
        }
        return 1.00;
    }

    protected function getSpecialChargeDefaultPoint()
    {
        switch ($this->battle->weapon->special->key ?? null) {
            case null:
                return null;
            case 'supershot':
                return 220;
            case 'supersensor':
                return 200;
            case 'daioika':
                return 200;
            case 'megaphone':
                return 160;
            default:
                return 180;
        }
    }

    protected function getSpecialDurationDefaultSec()
    {
        switch ($this->battle->weapon->special->key ?? null) {
            case null:
                return null;
            case 'barrier':
                return 5;
            case 'supersensor':
                return 12;
            default:
                return 6;
        }
    }
}
