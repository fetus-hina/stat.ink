<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class Ability2Info extends Model
{
    private const WEIGHT_LIGHT = 0;
    private const WEIGHT_MEDIUM = 1;
    private const WEIGHT_HEAVY = 2;

    public $weapon;
    public $version;
    public $ability;
    public $primary = 0;
    public $secondary = 0;

    public function getIsPrimaryOnly(): bool
    {
        return $this->ability && $this->ability->primary_only;
    }

    public function get57Format(): int
    {
        return max(0, min(
            $this->primary * 10 + $this->secondary * 3,
            57
        ));
    }

    // https://wikiwiki.jp/splatoon2mix/%E6%A4%9C%E8%A8%BC/%E3%82%AE%E3%82%A2%E3%83%91%E3%83%AF%E3%83%BC
    public function getCoefficient(bool $raw = false)
    {
        switch ($this->ability->key ?? null) {
            case 'ink_resistance_up': // {{{
                if (!$values = $this->getInkResistanceUpCoefficient()) {
                    return null;
                }
                return $raw
                    ? $values
                    : implode("\n", [
                        Yii::t('app-ability2', 'DoT: {perFrame} per frame', [
                            'perFrame' => Yii::$app->formatter->asDecimal($values['slipPerFrame'], 1),
                        ]),
                        Yii::t('app-ability2', 'DoT Delay: {frame} frames', [
                            'frame' => Yii::$app->formatter->asInteger($values['slipIgnoreFrame']),
                        ]),
                        Yii::t('app-ability2', 'DoT Cap: {damage}', [
                            'damage' => Yii::$app->formatter->asDecimal($values['slipCap'], 1),
                        ]),
                        Yii::t('app-ability2', 'Run Speed: {value}', [
                            'value' => Yii::t('app-ability2', 'Default×{pct}', [
                                'pct' => Yii::$app->formatter->asPercent($values['runSpeed'], 1),
                            ]),
                        ]),
                    ]);
                // }}}

            case 'ink_saver_main': // {{{
                if (!$value = $this->getInkSaverMainCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 1),
                    ]);
                // }}}

            case 'ink_saver_sub': // {{{
                if (!$value = $this->getInkSaverSubCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 1),
                    ]);
                // }}}

            case 'run_speed_up': // {{{
                if (!$values = $this->getRunSpeedUpCoefficient()) {
                    return null;
                }
                return $raw
                    ? $values
                    : implode("\n", [
                        Yii::t('app-ability2', 'Normal: {value}', [
                            'value' => Yii::t('app-ability2', 'Default×{pct}', [
                                'pct' => Yii::$app->formatter->asPercent($values[0], 1),
                            ]),
                        ]),
                        Yii::t('app-ability2', 'Shooting: {value}', [
                            'value' => Yii::t('app-ability2', 'Default×{pct}', [
                                'pct' => Yii::$app->formatter->asPercent($values[1], 1),
                            ]),
                        ]),
                    ]);
                // }}}

            case 'special_charge_up': // {{{
                if (!$value = $this->getSpecialChargeUpCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 1),
                    ]);
                // }}}

            case 'swim_speed_up': // {{{
                if (!$value = $this->getSwimSpeedUpCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 1),
                    ]);
                // }}}

            default:
                return null;
        }
    }

    private function getInkResistanceUpCoefficient(): ?array
    {
        // {{{
        $gp = $this->get57Format();
        $floor = function (float $value, int $precision): float {
            $fig = pow(10, $precision);
            return floor($value * $fig) / $fig;
        };

        return [
            'slipPerFrame' => $floor(0.3 - static::calcCoefficient($gp, 0.15), 1),
            'slipIgnoreFrame' => (int)ceil(static::calcCoefficient($gp, 39, 0, 2 / 3)),
            'slipCap' => round(40 - static::calcCoefficient($gp, 20), 1),
            'runSpeed' => (0.24 + static::calcCoefficient($gp, 0.48)) / 0.24,
        ];
        // }}}
    }

    private function getInkSaverMainCoefficient(): ?float
    {
        // {{{
        if (!$this->weapon) {
            return null;
        }

        $gp = $this->get57Format();
        switch ($this->weapon->mainReference->key ?? null) {
            case 'campingshelter':
            case 'dynamo':
            case 'h3reelgun':
            case 'hydra':
            case 'liter4k':
            case 'nova':
            case 'prime':
                if (
                    $this->version &&
                    version_compare($this->version->tag, '1.3.0', '<')
                ) {
                    return 1 - static::calcCoefficient($gp, 0.5);
                } else {
                    return 1 - static::calcCoefficient($gp, 0.5, 0, 0.6);
                }

            default:
                return 1 - static::calcCoefficient($gp, 0.45);
        }
        // }}}
    }

    private function getInkSaverSubCoefficient(): ?float
    {
        // {{{
        if (!$this->weapon || !$this->weapon->subweapon) {
            return null;
        }

        $gp = $this->get57Format();
        if (!$subKey = $this->weapon->subweapon->key) {
            return null;
        }

        if ($this->version) {
            $vTag = $this->version->tag;
            switch (true) {
                case version_compare($vTag, '2.0.0', '<'):
                    switch ($subKey) {
                        case 'curlingbomb':
                        case 'jumpbeacon':
                        case 'kyubanbomb':
                        case 'pointsensor':
                        case 'poisonmist':
                        case 'splashbomb':
                        case 'splashshield':
                        case 'sprinkler':
                        case 'trap':
                            return 1 - static::calcCoefficient($gp, 0.35);
                        default:
                            return 1 - static::calcCoefficient($gp, 0.3);
                    }
                    break;

                case version_compare($vTag, '4.3.0', '<'):
                    switch ($subKey) {
                        case 'curlingbomb':
                        case 'jumpbeacon':
                        case 'kyubanbomb':
                        case 'pointsensor':
                        case 'splashbomb':
                        case 'splashshield':
                        case 'sprinkler':
                        case 'tansanbomb':
                        case 'trap':
                            return 1 - static::calcCoefficient($gp, 0.35);

                        case 'poisonmist':
                        case 'robotbomb':
                            return 1 - static::calcCoefficient($gp, 0.3);

                        default:
                            return 1 - static::calcCoefficient($gp, 0.2);
                    }
                    break;

                case version_compare($vTag, '4.5.0', '<'):
                    switch ($subKey) {
                        case 'jumpbeacon':
                        case 'pointsensor':
                        case 'sprinkler':
                        case 'trap':
                            return 1 - static::calcCoefficient($gp, 0.4);

                        case 'curlingbomb':
                        case 'kyubanbomb':
                        case 'splashbomb':
                        case 'splashshield':
                            return 1 - static::calcCoefficient($gp, 0.35);

                        case 'poisonmist':
                        case 'robotbomb':
                        case 'tansanbomb':
                            return 1 - static::calcCoefficient($gp, 0.3);

                        default:
                            return 1 - static::calcCoefficient($gp, 0.2);
                    }
                    break;
            }
        }

        switch ($subKey) {
            case 'jumpbeacon':
            case 'sprinkler':
            case 'trap':
                return 1 - static::calcCoefficient($gp, 0.4);

            case 'curlingbomb':
            case 'kyubanbomb':
            case 'splashbomb':
            case 'splashshield':
                return 1 - static::calcCoefficient($gp, 0.35);

            case 'pointsensor':
            case 'poisonmist':
            case 'robotbomb':
            case 'tansanbomb':
                return 1 - static::calcCoefficient($gp, 0.3);

            default:
                return 1 - static::calcCoefficient($gp, 0.2);
        }
        // }}}
    }

    private function getRunSpeedUpCoefficient(): ?array
    {
        // {{{
        if (!$this->weapon || !$this->weapon->mainReference) {
            return null;
        }

        $gp = $this->get57Format();
        $key = $this->weapon->mainReference->key;
        switch ($key) {
            case 'hydra':
                return [
                    1 + static::calcCoefficient($gp, 0.56),
                    1 + static::calcCoefficient($gp, 0.35),
                ];

            case 'nautilus47':
                return [
                    1 + static::calcCoefficient($gp, 0.48),
                    1 + static::calcCoefficient($gp, 0.3),
                ];

            case 'barrelspinner':
                return [
                    1 + static::calcCoefficient($gp, 0.48),
                    1 + static::calcCoefficient($gp, 0.35),
                ];

            case 'splatspinner':
                return [
                    1 + static::calcCoefficient($gp, 0.48),
                    1 + static::calcCoefficient($gp, 0.3),
                ];

            case 'kugelschreiber': // クーゲルはスピナーだが特殊扱いがない
            default:
                switch (static::weaponWeight($key)) {
                    case static::WEIGHT_LIGHT:
                        return [
                            1 + static::calcCoefficient($gp, 0.4),
                            1 + static::calcCoefficient($gp, 0.25),
                        ];

                    case static::WEIGHT_MEDIUM:
                        return [
                            1 + static::calcCoefficient($gp, 0.48),
                            1 + static::calcCoefficient($gp, 0.25),
                        ];

                    case static::WEIGHT_HEAVY:
                        return [
                            1 + static::calcCoefficient($gp, 0.56),
                            1 + static::calcCoefficient($gp, 0.25),
                        ];
                }
        }

        return null;
        // }}}
    }

    private function getSpecialChargeUpCoefficient(): ?float
    {
        // {{{
        $gp = $this->get57Format();
        return 1 - static::calcCoefficient($gp, 0.3);
        // }}}
    }

    private function getSwimSpeedUpCoefficient(): ?float
    {
        // {{{
        if (!$this->weapon || !$this->weapon->mainReference) {
            return null;
        }

        $gp = $this->get57Format();
        switch (static::weaponWeight($this->weapon->mainReference->key)) {
            case static::WEIGHT_LIGHT:
                return 1 + static::calcCoefficient($gp, 0.384);

            case static::WEIGHT_MEDIUM:
                return 1 + static::calcCoefficient($gp, 0.48);

            case static::WEIGHT_HEAVY:
                return 1 + static::calcCoefficient($gp, 0.672);
        }

        return null;
        // }}}
    }

    private static function calcCoefficient(
        int $gp,
        float $max,
        float $min = 0.0,
        float $mid = 0.5
    ): ?float {
        if ($gp < 1) {
            return null;
        }

        $gp = min($gp, 57);
        $x = min(0.033 * $gp - 0.00027 * $gp * $gp, 1.0);
        return $min + ($max - $min) * pow($x, log($mid) / log(0.5));
    }

    private static function weaponWeight(string $key): int
    {
        switch ($key) {
            case 'bamboo14mk1':
            case 'bold':
            case 'carbon':
            case 'clashblaster':
            case 'hissen':
            case 'nova':
            case 'nzap85':
            case 'pablo':
            case 'promodeler_mg':
            case 'sputtery':
            case 'spygadget':
            case 'wakaba':
                return static::WEIGHT_LIGHT;


            case 'campingshelter':
            case 'dynamo':
            case 'explosher':
            case 'hydra':
            case 'liter4k':
                return static::WEIGHT_HEAVY;

            default:
                return static::WEIGHT_MEDIUM;
        }
    }
}
