<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\ability\effect;

use app\models\Battle;
use yii\base\Component;

use function ceil;
use function pow;

/**
 * @property-read float|null $attackPct
 * @property-read float|null $bombThrowPct
 * @property-read string $calculatorVersion
 * @property-read float|null $defensePct
 * @property-read float|null $inkRecoverySec
 * @property-read float|null $inkUsePctMain
 * @property-read float|null $inkUsePctSub
 * @property-read float|null $markingPct
 * @property-read float|null $respawnSec
 * @property-read float|null $runSpeedPct
 * @property-read int|null $specialChargePoint
 * @property-read int|null $specialDurationCount
 * @property-read float|null $specialDurationSec
 * @property-read int|float|null $specialLossPct
 * @property-read array<string, float>|null $superJumpSecs
 * @property-read float|null $swimSpeedPct
 */
abstract class Base extends Component
{
    public const SPECIAL_DURATION_40PCT = 75;
    public const SPECIAL_DURATION_60PCT = 50;

    public ?Battle $battle = null;
    public ?string $version = null;
    /**
     * @var array<string, int|null>
     */
    private array $countCache = [];

    abstract public function getCalculatorVersion(): string;

    abstract public function getAttackPct(): ?float;

    abstract public function getDefensePct(): ?float;

    abstract public function getInkUsePctMain(): ?float;

    abstract public function getInkUsePctSub(): ?float;

    abstract public function getInkRecoverySec(): ?float;

    abstract public function getRunSpeedPct(): ?float;

    abstract public function getSwimSpeedPct(): ?float;

    abstract public function getSpecialChargePoint(): ?int;

    abstract public function getSpecialDurationSec(): ?float;

    abstract public function getSpecialLossPct(): int|float|null;

    abstract public function getRespawnSec(): ?float;

    /**
     * @return array<string, float>|null
     */
    abstract public function getSuperJumpSecs(): ?array;

    abstract public function getBombThrowPct(): ?float;

    abstract public function getMarkingPct(): ?float;

    public function getSpecialDurationCount(): ?int
    {
        $interval = $this->getSpecialFrameInterval();
        if ($interval === null || $interval < 1) {
            return null;
        }
        return (int)ceil($this->getSpecialDurationSec() * 60 / $interval);
    }

    protected function getSpecialFrameInterval(): ?int
    {
        switch ($this->battle->weapon->special->key ?? null) {
            case 'quickbomb':
                return 22;
            case 'splashbomb':
                return 33;
            case 'kyubanbomb':
                return 33;
            case 'chasebomb':
                return 38;
            case 'supershot':
                return 64;
            default:
                return null;
        }
    }

    /**
     * @param int|float|numeric-string $baseDamage
     */
    public function calcDamage(int|float|string $baseDamage, int $defMain, int $defSub): float
    {
        $def = $defMain * 10 + $defSub * 3;
        return $this->calcDamageImpl(
            $baseDamage,
            $this->calcX('damage_up', 100),
            ((0.99 * $def) - pow(0.09 * $def, 2)) / 100,
        );
    }

    /**
     * @param int|float|numeric-string $baseDamage
     */
    protected function calcDamageImpl(int|float|string $baseDamage, ?float $a, float $d): float
    {
        $x = $a >= $d ? 1 + $a - $d : (1 + ($a - $d) / 1.8);
        return $baseDamage * $x;
    }

    protected function getEffectiveCount(string $key): ?int
    {
        if (!isset($this->countCache[$key])) {
            $this->countCache[$key] = $this->getEffectiveCountImpl($key);
        }
        return $this->countCache[$key];
    }

    protected function getEffectiveCountImpl(string $key): ?int
    {
        $gears = [
            $this->battle->headgear,
            $this->battle->clothing,
            $this->battle->shoes,
        ];
        $main = 0;
        $sub = 0;
        foreach ($gears as $gear) {
            if (!$gear) {
                return null;
            }
            if ($gear->primaryAbility) {
                if ($gear->primaryAbility->key === $key) {
                    ++$main;
                }
            }
            if ($gear->secondaries) {
                foreach ($gear->secondaries as $secondary) {
                    if ($secondary->ability) {
                        if ($secondary->ability->key === $key) {
                            ++$sub;
                        }
                    }
                }
            }
        }
        return $main * 10 + $sub * 3;
    }

    protected function calcX(string $key, int|float $divBy): ?float
    {
        $a = $this->getEffectiveCount($key);
        if ($a === null) {
            return null;
        }
        return ((0.99 * $a) - pow(0.09 * $a, 2)) / $divBy;
    }
}
