<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\ability\effect;

use Override;

use function ceil;
use function max;
use function round;

class v020500 extends Base
{
    #[Override]
    public function getCalculatorVersion(): string
    {
        return '2.5.0';
    }

    #[Override]
    public function getAttackPct(): ?float
    {
        $x = $this->calcX('damage_up', 100);
        if ($x === null) {
            return $x;
        }
        return 1 + $x;
    }

    #[Override]
    public function getDefensePct(): ?float
    {
        $x = $this->calcX('defense_up', 100);
        if ($x === null) {
            return $x;
        }
        return 1 + $x;
    }

    #[Override]
    public function getInkUsePctMain(): ?float
    {
        $x = $this->calcX('ink_saver_main', 75);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    #[Override]
    public function getInkUsePctSub(): ?float
    {
        $x = $this->calcX('ink_saver_sub', 120);
        if ($x === null) {
            return null;
        }
        return 1 - $x;
    }

    #[Override]
    public function getInkRecoverySec(): ?float
    {
        $x = $this->calcX('ink_recovery_up', 75);
        if ($x === null) {
            return null;
        }
        $f = 100 / (180 * (1 - $x));
        return ceil(100 / $f) / 60;
    }

    #[Override]
    public function getRunSpeedPct(): ?float
    {
        $x = $this->calcX('run_speed_up', 60);
        if ($x === null) {
            return null;
        }
        return 1 + $x;
    }

    #[Override]
    public function getSwimSpeedPct(): ?float
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

    #[Override]
    public function getSpecialChargePoint(): ?int
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

    #[Override]
    public function getSpecialDurationSec(): ?float
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

    #[Override]
    public function getSpecialLossPct(): int|float|null
    {
        $x = $this->calcX('special_saver', 60);
        if ($x === null) {
            return null;
        }
        return max(0.5 - $x, 0);
    }

    #[Override]
    public function getRespawnSec(): ?float
    {
        $x = $this->calcX('quick_respawn', 45);
        if ($x === null) {
            return null;
        }
        return ((1 - $x) * 360 + 30 + 120) / 60;
    }

    #[Override]
    public function getSuperJumpSecs(): ?array
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

    #[Override]
    public function getBombThrowPct(): ?float
    {
        $x = $this->calcX('bomb_range_up', 60);
        if ($x === null) {
            return null;
        }
        return 1 + $x;
    }

    #[Override]
    public function getMarkingPct(): ?float
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

    protected function getSpecialChargeDefaultPoint(): ?int
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

    protected function getSpecialDurationDefaultSec(): int|float|null
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
