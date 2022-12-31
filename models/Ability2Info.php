<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
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
    public $haveNinja = false;

    public function getIsPrimaryOnly(): bool
    {
        return $this->ability && $this->ability->primary_only;
    }

    public function get57Format(): int
    {
        return max(0, min(
            $this->primary * 10 + $this->secondary * 3,
            57,
        ));
    }

    // @codingStandardsIgnoreStart
    // https://mntone.minibird.jp/splw/%E3%82%AB%E3%83%86%E3%82%B4%E3%83%AA:%E3%82%AE%E3%82%A2%E3%83%91%E3%83%AF%E3%83%BC
    // @codingStandardsIgnoreEnd
    public function getCoefficient(bool $raw = false)
    {
        switch ($this->ability->key ?? null) {
            case 'ink_resistance_up': // {{{
                if (!$values = $this->getInkResistanceUpCoefficient()) {
                    return null;
                }

                if ($raw) {
                    return $values;
                }

                $f = Yii::$app->formatter;
                $rows = [];
                if (($values['slipPerFrame'] ?? null) !== null) {
                    $rows[] = Yii::t('app-ability2', 'DoT: {perFrame} per frame', [
                        'perFrame' => $f->asDecimal($values['slipPerFrame'], 1),
                    ]);
                }
                if (($values['slipIgnoreFrame'] ?? null) !== null) {
                    $rows[] = Yii::t('app-ability2', 'DoT Delay: {frame} frames ({sec} sec.)', [
                        'frame' => $f->asInteger($values['slipIgnoreFrame']),
                        'sec' => $f->asDecimal($values['slipIgnoreFrame'] / 60, 3),
                    ]);
                }
                if (($values['slipCap'] ?? null) !== null) {
                    $rows[] = Yii::t('app-ability2', 'DoT Cap: {damage}', [
                        'damage' => $f->asDecimal($values['slipCap'], 1),
                    ]);
                }
                if (
                    ($values['runSpeed'] ?? null) !== null &&
                    ($values['runSpeedShoot'] ?? null) !== null
                ) {
                    $rows[] = Yii::t('app-ability2', 'Run Speed: {value}', [
                        'value' => Yii::t('app-ability2', 'Default×{pct}', [
                            'pct' => $f->asPercent($values['runSpeed'], 1),
                        ]),
                    ]);
                    $rows[] = Yii::t('app-ability2', 'Shooting: {value}', [
                        'value' => Yii::t('app-ability2', 'Default×{pct}', [
                            'pct' => $f->asPercent($values['runSpeedShoot'], 1),
                        ]),
                    ]);
                    if (($values['runSpeedCharge'] ?? null) !== null) {
                        $rows[] = Yii::t('app-ability2', 'Charging: {value}', [
                            'value' => Yii::t('app-ability2', 'Default×{pct}', [
                                'pct' => $f->asPercent($values['runSpeedCharge'], 1),
                            ]),
                        ]);
                    }
                }

                foreach ($rows as $row) {
                    if (strpos($row, 'DoT') !== false) {
                        $rows[] = Yii::t('app', '"DoT": "Damage over time"');
                        break;
                    }
                }

                return implode("\n", $rows);
                // }}}

            case 'ink_saver_main': // {{{
                if (!$value = $this->getInkSaverMainCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 2),
                    ]);
                // }}}

            case 'ink_saver_sub': // {{{
                if (!$value = $this->getInkSaverSubCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 2),
                    ]);
                // }}}

            case 'main_power_up':
                if (!$values = $this->getMainPowerUpCoefficient()) {
                    return null;
                }

                $f = Yii::$app->formatter;
                $rows = [];
                if (
                    (($values['baseDamage'] ?? null) !== null) &&
                    (($values['damage'] ?? null) !== null) &&
                    (($values['damageRate'] ?? null) !== null) &&
                    (($values['damageCap'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t(
                        'app-ability2',
                        $values['damageCap'] < $values['damage']
                            ? 'Damage: {damageCap} = {baseDamage}×{percent} ({damage}, capped)'
                            : 'Damage: {damage} = {baseDamage}×{percent}',
                        [
                            'baseDamage' => $f->asDecimal($values['baseDamage'], 1),
                            'damage' => $f->asDecimal($values['damage'], 1),
                            'damageCap' => $f->asDecimal($values['damageCap'], 1),
                            'percent' => $f->asPercent($values['damageRate'], 1),
                        ],
                    );
                }

                $list = [
                    'H' => 'Horizontal',
                    'V' => 'Vertical',
                    'Sq' => 'Squish',
                    'Long' => 'Long',
                    'Short' => 'Short',
                ];
                foreach ($list as $suffix => $textPrefix) {
                    if (
                        (($values['baseDamage' . $suffix] ?? null) !== null) &&
                        (($values['damage' . $suffix] ?? null) !== null) &&
                        (($values['damageRate' . $suffix] ?? null) !== null) &&
                        (($values['damageCap' . $suffix] ?? null) !== null)
                    ) {
                        $rows[] = vsprintf('(%s) %s', [
                            Yii::t('app-ability2', $textPrefix),
                            Yii::t(
                                'app-ability2',
                                $values['damageCap' . $suffix] < $values['damage' . $suffix]
                                    ? 'Damage: {damageCap} = {baseDamage}×{percent} ({damage}, capped)'
                                    : 'Damage: {damage} = {baseDamage}×{percent}',
                                [
                                    'baseDamage' => $f->asDecimal($values['baseDamage' . $suffix], 1),
                                    'damage' => $f->asDecimal($values['damage' . $suffix], 1),
                                    'damageCap' => $f->asDecimal($values['damageCap' . $suffix], 1),
                                    'percent' => $f->asPercent($values['damageRate' . $suffix], 1),
                                ],
                            ),
                        ]);
                    }
                }

                return implode("\n", $rows);

            case 'run_speed_up': // {{{
                if (!$values = $this->getRunSpeedUpCoefficient()) {
                    return null;
                }

                if ($raw) {
                    return $values;
                }

                $f = Yii::$app->formatter;
                $rows = [];

                if (($values['runSpeedRatio'] ?? null) !== null) {
                    $rows[] = Yii::t('app-ability2', 'Normal: {value}', [
                        'value' => ($values['runSpeedDUPF'] ?? null) !== null
                            ? Yii::t('app-ability2', '{pct} ({dupf} DU/f)', [
                                'pct' => $f->asPercent($values['runSpeedRatio'], 1),
                                'dupf' => $f->asDecimal($values['runSpeedDUPF'], 3),
                            ])
                            : Yii::t('app-ability2', 'Default×{pct}', [
                                'pct' => $f->asPercent($values['runSpeedRatio'], 1),
                            ]),
                    ]);
                }

                return implode("\n", $rows);
                // }}}

            case 'special_charge_up': // {{{
                if (!$value = $this->getSpecialChargeUpCoefficient()) {
                    return null;
                }
                return $raw
                    ? $value
                    : Yii::t('app-ability2', 'Default×{pct}', [
                        'pct' => Yii::$app->formatter->asPercent($value, 2),
                    ]);
                // }}}

            case 'special_power_up': // {{{
                if (!$values = $this->getSpecialPowerUpCoefficient()) {
                    return null;
                }

                if ($raw) {
                    return $values;
                }
                // var_dump($values);

                $f = Yii::$app->formatter;
                $rows = [];
                if (
                    (($values['durationFrames'] ?? null) !== null) &&
                    (($values['durationRate'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t('app-ability2', 'Duration: {pct} ({sec} sec., {frames} frames)', [
                        'pct' => $f->asPercent($values['durationRate'], 1),
                        'sec' => $f->asDecimal($values['durationFrames'] / 60, 3),
                        'frames' => $f->asInteger($values['durationFrames']),
                    ]);
                }

                if (
                    (($values['armorDurationFrames1'] ?? null) !== null) &&
                    (($values['armorDurationFrames2'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t('app-ability2', 'Duration: {sec} ({sec1}+{sec2}) sec', [
                        'sec' => $f->asDecimal(
                            ($values['armorDurationFrames1'] + $values['armorDurationFrames2']) / 60,
                            3,
                        ),
                        'sec1' => $f->asDecimal($values['armorDurationFrames1'] / 60, 3),
                        'sec2' => $f->asDecimal($values['armorDurationFrames2'] / 60, 3),
                        // 'frames1' => $f->asInteger($values['armorDurationFrames1']),
                        // 'frames2' => $f->asInteger($values['armorDurationFrames2']),
                    ]);
                }

                if (
                    (($values['blastAreaNear'] ?? null) !== null) &&
                    (($values['blastAreaNearRate'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t('app-ability2', 'Damage R. (near): {pct} ({radius})', [
                        'pct' => $f->asPercent($values['blastAreaNearRate'], 1),
                        'radius' => $f->asDecimal($values['blastAreaNear'], 2),
                    ]);
                }

                if (
                    (($values['blastAreaFar'] ?? null) !== null) &&
                    (($values['blastAreaFarRate'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t('app-ability2', 'Damage R. (far): {pct} ({radius})', [
                        'pct' => $f->asPercent($values['blastAreaFarRate'], 1),
                        'radius' => $f->asDecimal($values['blastAreaFar'], 2),
                    ]);
                }

                if (
                    (($values['inkRadius'] ?? null) !== null) &&
                    (($values['inkRadiusRatio'] ?? null) !== null)
                ) {
                    $rows[] = Yii::t('app-ability2', 'Inking R.: {pct} ({radius})', [
                        'pct' => $f->asPercent($values['inkRadiusRatio'], 1),
                        'radius' => $f->asDecimal($values['inkRadius'], 2),
                    ]);
                }

                return implode("\n", $rows);
                // }}}

            case 'swim_speed_up': // {{{
                if (!$values = $this->getSwimSpeedUpCoefficient()) {
                    return null;
                }

                if ($raw) {
                    return $values;
                }

                $f = Yii::$app->formatter;
                $rows = [];

                if (($values['swimSpeedRatio'] ?? null) !== null) {
                    if (($values['swimSpeedDUPF'] ?? null) !== null) {
                        $rows[] = Yii::t('app-ability2', '{pct} ({dupf} DU/f)', [
                            'pct' => $f->asPercent($values['swimSpeedRatio'], 1),
                            'dupf' => $f->asDecimal($values['swimSpeedDUPF'], 3),
                        ]);
                    } else {
                        $rows[] = Yii::t('app-ability2', 'Default×{pct}', [
                            'pct' => $f->asPercent($values['swimSpeedRatio'], 1),
                        ]);
                    }

                    if ($this->haveNinja) {
                        $rows[] = Yii::t('app-ability2', 'Revised by {ability}', [
                            'ability' => Yii::t('app-ability2', 'Ninja Squid'),
                        ]);
                    }
                }

                return implode("\n", $rows);
                // }}}

            default:
                return null;
        }
    }

    private function getInkResistanceUpCoefficient(): ?array
    {
        // {{{
        if (!$gp = $this->get57Format()) {
            return null;
        }

        $floor = function (float $value, int $precision): float {
            $fig = pow(10, $precision);
            return floor($value * $fig) / $fig;
        };

        $vTag = $this->version->tag ?? '9999.999.999';
        $slipIgnoreFrame = (function () use ($gp, $vTag): ?int {
            // {{{
            switch (true) {
                case version_compare($vTag, '4.3.0', '<'):
                    return null;

                case version_compare($vTag, '4.4.0', '<'):
                    return (int)ceil(static::calcCoefficient($gp, 30, 0, 2 / 3));

                default:
                    return (int)ceil(static::calcCoefficient($gp, 39, 0, 2 / 3));
            }
            // }}}
        })();
        $runSpeed = (function () use ($gp, $vTag): float {
            // {{{
            if (version_compare($vTag, '4.6.0', '<')) {
                return static::calcCoefficient($gp, 0.72, 0.24, 0.5) / 0.24;
            }

            return static::calcCoefficient($gp, 0.769, 0.24, 0.6) / 0.24;
            // }}}
        })();
        $runSpeedShoot = (function () use ($gp, $vTag): float {
            // {{{
            if (version_compare($vTag, '4.6.0', '<')) {
                return static::calcCoefficient($gp, 0.40, 0.12, 0.5) / 0.12;
            }

            return static::calcCoefficient($gp, 0.42, 0.12, 0.7) / 0.12;
            // }}}
        })();
        $runSpeedCharge = (function () use ($gp): ?float {
            // {{{
            if (!$this->weapon || !$this->weapon->mainReference) {
                return null;
            }

            switch ($this->weapon->mainReference->key) {
                case 'splatspinner':
                    return 0.7 * static::calcCoefficient($gp, 1, 0.5) / (0.7 * 0.5);

                case 'barrelspinner':
                    return 0.6 * static::calcCoefficient($gp, 1, 0.5) / (0.6 * 0.5);

                case 'hydra':
                case 'nautilus47':
                    return 0.4 * static::calcCoefficient($gp, 1, 0.5) / (0.4 * 0.5);

                case 'kugelschreiber':
                    return 0.96 * static::calcCoefficient($gp, 1, 0.5) / (0.96 * 0.5);
            }

            return null;
            // }}}
        })();

        return [
            'slipPerFrame' => $floor(0.3 - static::calcCoefficient($gp, 0.15), 1),
            'slipIgnoreFrame' => $slipIgnoreFrame,
            'slipCap' => round(40 - static::calcCoefficient($gp, 20), 1),
            'runSpeed' => $runSpeed,
            'runSpeedShoot' => $runSpeedShoot,
            'runSpeedCharge' => $runSpeedCharge,
        ];
        // }}}
    }

    private function getInkSaverMainCoefficient(): ?float
    {
        // {{{
        if (!$this->weapon || !$this->weapon->mainReference) {
            return null;
        }

        if (!$gp = $this->get57Format()) {
            return null;
        }

        switch ($this->weapon->mainReference->key) {
            case 'campingshelter':
            case 'dynamo':
            case 'h3reelgun':
            case 'hydra':
            case 'liter4k':
            case 'nova':
            case 'prime':
                if (
                    $this->version &&
                    version_compare($this->version->tag, '1.4.0', '<')
                ) {
                    return static::calcCoefficient($gp, 0.5, 1, 0.5);
                } else {
                    return static::calcCoefficient($gp, 0.5, 1, 0.6);
                }

            default:
                return static::calcCoefficient($gp, 0.55, 1, 0.5);
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
                case version_compare($vTag, '4.3.0', '<'):
                    // よくわからない
                    return null;
            }
        }

        switch ($subKey) {
            case 'quickbomb':
                return static::calcCoefficient($gp, 0.8, 1.0, 0.5);

            case 'poisonmist':
            case 'robotbomb':
            case 'tansanbomb':
                return static::calcCoefficient($gp, 0.7, 1.0, 0.5);

            case 'jumpbeacon':
            case 'sprinkler':
            case 'trap':
                return static::calcCoefficient($gp, 0.6, 1.0, 0.5);

            default:
                return static::calcCoefficient($gp, 0.65, 1.0, 0.5);
        }
        // }}}
    }

    private function getMainPowerUpCoefficient(): ?array
    {
        if (!$this->weapon || !$this->weapon->mainPowerUp || !$this->version) {
            return null;
        }

        switch ($this->weapon->mainPowerUp->key) {
            case 'damage':
                return $this->getMainPowerUpCoefficientIncreaseDamage(
                    $this->weapon->mainReference,
                    $this->weapon->getWeaponAttack($this->version),
                    $this->version,
                    $this->get57Format(),
                );

            default:
                return null;
        }
    }

    private function getMainPowerUpCoefficientIncreaseDamage(
        ?Weapon2 $weapon,
        ?WeaponAttack2 $attack,
        ?SplatoonVersion2 $version,
        ?int $gp
    ): ?array {
        if (!$weapon || !$attack || !$version || !$gp) {
            return null;
        }

        $getMaxDamage = function (float $baseDamage): float {
            if ($baseDamage >= 100.0) {
                return 300.0;
            }
            for ($i = 1;; ++$i) {
                if ($baseDamage > 100.0 / ($i + 1)) {
                    return floor(((100.0 / $i) - 0.01) * 10) / 10;
                }
            }
            return 0.0;
        };

        $calcDamage = function (
            float $baseDamage,
            float $maxRate,
            float $mid = 0.5,
            ?string $suffix = null
        ) use (
            $gp,
            $getMaxDamage
        ): array {
            $maxDamage = $getMaxDamage($baseDamage);
            $c = static::calcCoefficient($gp, $maxRate, 1.0);
            $damage = floor($baseDamage * $c * 10.0) / 10.0;
            $suffix = ucfirst(trim((string)$suffix));
            return [
                'baseDamage' . $suffix => $baseDamage,
                'damageRate' . $suffix => $c,
                'damage' . $suffix => $damage,
                'damageCap' . $suffix => $maxDamage,
            ];
        };

        $baseDamage = (float)$attack->damage;
        switch ($weapon->key) {
            case 'bold':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '4.3.1', '<') ? 1.2 : 1.25,
                );

            case 'prime':
                if (version_compare($version->tag, '4.6.0', '<')) {
                    return $calcDamage($baseDamage, 1.25);
                } elseif (version_compare($version->tag, '5.0.0', '<')) {
                    return $calcDamage($baseDamage, 1.216);
                } else {
                    return $calcDamage($baseDamage, 1.208);
                }

            case 'l3reelgun':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '5.1.0', '<') ? 1.3 : 1.24,
                );

            case 'h3reelgun':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '4.5.0', '<') ? 1.25 : 1.24,
                );

            case 'bottlegeyser':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '4.3.1', '<') ? 1.2 : 1.3,
                );

            case 'carbon':
            case 'dynamo':
                return array_merge(
                    $calcDamage((float)$attack->damage, 1.15, 0.5, 'H'),
                    $calcDamage((float)$attack->damage2, 1.15, 0.5, 'V'),
                    $calcDamage((float)$attack->damage3, 1.15, 0.5, 'Sq'),
                );

            case 'splatroller':
            case 'variableroller':
                return array_merge(
                    $calcDamage((float)$attack->damage, 1.15, 2 / 3, 'H'),
                    $calcDamage((float)$attack->damage2, 1.15, 2 / 3, 'V'),
                    $calcDamage((float)$attack->damage3, 1.15, 2 / 3, 'Sq'),
                );
                return null;

            case 'sputtery':
            case 'kelvin525':
            case 'dualsweeper':
            case 'quadhopper_black':
                return $calcDamage($baseDamage, 1.2);

            case 'maneuver':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '4.3.1', '<') ? 1.2 : 1.16,
                    version_compare($version->tag, '4.3.1', '<') ? 0.5 : 0.375,
                );

            case 'splatcharger':
                return $calcDamage($baseDamage, 1.2);

            case 'bamboo14mk1':
                return $calcDamage(
                    $baseDamage,
                    version_compare($version->tag, '5.1.0', '<') ? 1.2 : 1.19,
                );

            case 'soytuber':
                return $calcDamage($baseDamage, 1.5);

            case 'hydra':
                return $calcDamage($baseDamage, 1.2);

            case 'kugelschreiber':
                if (version_compare($version->tag, '4.3.1', '<')) {
                    return $calcDamage(32.0, 1.2);
                } elseif (version_compare($version->tag, '4.4.0', '<')) {
                    return $calcDamage(32.0, 1.1);
                }
                return array_merge(
                    $calcDamage((float)$attack->damage, 1.1, 0.5, 'Long'),
                    $calcDamage((float)$attack->damage2, 1.1, 0.5, 'Short'),
                );

            case 'sharp':
            case '96gal':
                return $calcDamage($baseDamage, 1.25);
        }

        return null;
    }

    private function getRunSpeedUpCoefficient(): ?array
    {
        // {{{
        if (!$this->weapon || !$this->weapon->mainReference) {
            return null;
        }

        if (!$gp = $this->get57Format()) {
            return null;
        }

        $results = [];
        $key = $this->weapon->mainReference->key;

        $calcRunSpeed = function (float $defaultSpeed) use ($gp): array {
            $c = static::calcCoefficient($gp, 1.44, $defaultSpeed);
            return [
                'runSpeedDUPF' => $c,
                'runSpeedRatio' => $c / $defaultSpeed,
            ];
        };
        switch (static::weaponWeight($key)) {
            case static::WEIGHT_LIGHT:
                $results = array_merge($results, $calcRunSpeed(1.04));
                break;

            case static::WEIGHT_MEDIUM:
                $results = array_merge($results, $calcRunSpeed(0.96));
                break;

            case static::WEIGHT_HEAVY:
                $results = array_merge($results, $calcRunSpeed(0.88));
                break;
        }

        // TODO: 射撃中速度計算

        return $results;
        // }}}
    }

    private function getSpecialChargeUpCoefficient(): ?float
    {
        // {{{
        if (!$gp = $this->get57Format()) {
            return null;
        }

        return 1 / static::calcCoefficient($gp, 1.3, 1.0);
        // }}}
    }

    private function getSpecialPowerUpCoefficient(): ?array
    {
        if (!$this->weapon || !$this->weapon->special) {
            return null;
        }

        if (!$gp = $this->get57Format()) {
            return null;
        }

        $duration = function (int $baseFrames, int $maxExtends) use ($gp): array {
            $c = (int)ceil(static::calcCoefficient($gp, $baseFrames + $maxExtends, $baseFrames));
            return [
                'durationFrames' => $c,
                'durationRate' => $c / $baseFrames,
            ];
        };
        $blastArea = function (
            string $tag,
            float $base,
            float $maxExtends,
            float $ratio = 0.5
        ) use ($gp): array {
            $c = static::calcCoefficient($gp, $base + $maxExtends, $base, $ratio);
            $tag = ucfirst($tag);
            return [
                "blastArea{$tag}" => $c,
                "blastArea{$tag}Rate" => $c / $base,
            ];
        };

        $key = $this->weapon->special->key;
        switch ($key) {
            case 'amefurashi':
                return $duration(480, 120);

            case 'armor':
                if ($this->version && version_compare($this->version->tag, '1.4.0', '<')) {
                    return [
                        'armorDurationFrames1' => 120,
                        'armorDurationFrames2' => (int)ceil(static::calcCoefficient($gp, 540, 360)),
                    ];
                }
                return [
                    'armorDurationFrames1' => (int)ceil(static::calcCoefficient($gp, 60, 120)),
                    'armorDurationFrames2' => (int)ceil(static::calcCoefficient($gp, 540, 360)),
                ];

            case 'bubble':
                // TODO
                break;

            case 'chakuchi':
                // TODO
                break;

            case 'curlingbomb_pitcher':
                return $duration(400, 120);

            case 'jetpack':
                $vTag = $this->version->tag ?? '9999.999.999';
                $inkC = static::calcCoefficient($gp, 4.0, 3.2);
                $ink = [
                    'inkRadius' => $inkC,
                    'inkRadiusRatio' => $inkC / 3.2,
                ];
                $results = $duration(450, 60);
                if (version_compare($vTag, '1.3.0', '<')) {
                    return array_merge(
                        $duration(480, 120),
                        $blastArea('near', 6, 0),
                        $blastArea('far', 3, 0),
                        $ink,
                    );
                } elseif (version_compare($vTag, '1.4.0', '<')) {
                    return array_merge(
                        $duration(480, 120),
                        $blastArea('near', 5, 0),
                        $blastArea('far', 3, 0),
                        $ink,
                    );
                } elseif (version_compare($vTag, '4.0.0', '<')) {
                    return array_merge(
                        $duration(480, 60),
                        $blastArea('near', 5, 1.5),
                        $blastArea('far', 2.5, 0.75),
                        $ink,
                    );
                }
                return array_merge(
                    $duration(450, 60),
                    $blastArea('near', 5, 1.5),
                    $blastArea('far', 2.5, 0.75),
                    $ink,
                );

            case 'kyubanbomb_pitcher':
                return $duration(360, 120);

            case 'missile':
                // TODO
                break;

            case 'nicedama':
                // TODO
                break;

            case 'presser':
                return $duration(430, 80);

            case 'quickbomb_pitcher':
                return $duration(360, 120);

            case 'robotbomb_pitcher':
                return $duration(360, 120);

            case 'sphere':
                // TODO
                break;

            case 'splashbomb_pitcher':
                return $duration(360, 120);

            case 'ultrahanko':
                return $duration(540, 120);
        }

        return null;
    }

    private function getSwimSpeedUpCoefficient(): ?array
    {
        // {{{
        if (!$this->weapon || !$this->weapon->mainReference) {
            return null;
        }

        if (!$gp = $this->get57Format()) {
            return null;
        }

        $calcSpeed = function (
            float $min,
            float $maxDiff,
            float $mid = 0.5,
            float $afterK = 1.0
        ) use ($gp): array {
            $c = static::calcCoefficient($gp, $min + $maxDiff, $min, $mid) * $afterK;
            return [
                'swimSpeedDUPF' => $c,
                'swimSpeedRatio' => $c / $min,
            ];
        };

        $weight = static::weaponWeight($this->weapon->mainReference->key);
        if ($this->version) {
            // イカニンジャをつけていれば 2.3.x までの計算式が特殊になる
            // そうでなければ以降の式と共通
            if ($this->haveNinja && version_compare($this->version->tag, '2.4.0', '<')) {
                switch ($weight) {
                    case static::WEIGHT_LIGHT:
                        return $calcSpeed(2.016, 0.384, 0.5, 0.9);

                    case static::WEIGHT_MEDIUM:
                        return $calcSpeed(1.92, 0.48, 0.5, 0.9);

                    case static::WEIGHT_HEAVY:
                        return $calcSpeed(1.728, 0.672, 0.5, 0.9);
                }
            }

            // 4.2.x までで修正する必要があるのは重量級だけ
            if (
                version_compare($this->version->tag, '4.3.0', '<') &&
                $weight === static::WEIGHT_HEAVY
            ) {
                return $this->haveNinja
                    ? $calcSpeed(1.728, 0.5376, 0.5, 0.9)
                    : $calcSpeed(1.728, 0.672, 0.5, 1.0);
            }
        }

        switch ($weight) {
            case static::WEIGHT_LIGHT:
                return $this->haveNinja
                    ? $calcSpeed(2.016, 0.3072, 0.5, 0.9)
                    : $calcSpeed(2.016, 0.384, 0.5, 1.0);

            case static::WEIGHT_MEDIUM:
                return $this->haveNinja
                    ? $calcSpeed(1.92, 0.384, 0.5, 0.9)
                    : $calcSpeed(1.92, 0.48, 0.5, 1.0);

            case static::WEIGHT_HEAVY:
                return $this->haveNinja
                    ? $calcSpeed(1.728, 0.5376, 0.64285714285, 0.9)
                    : $calcSpeed(1.728, 0.672, 0.64285714285, 1.0);
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
