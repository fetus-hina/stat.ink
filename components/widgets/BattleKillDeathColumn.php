<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\i18n\Formatter;

final class BattleKillDeathColumn extends Widget
{
    public ?int $kill = null;
    public ?int $death = null;
    public ?int $assist = null;
    public ?int $kill_or_assist = null;

    public ?Formatter $formatter = null;

    public function init()
    {
        parent::init();

        if ($this->formatter === null) {
            $this->formatter = Yii::$app->formatter;
        }
    }

    public function run()
    {
        return Html::tag(
            'div',
            \implode('', \array_map(
                fn (string $html): string => Html::tag('div', $html),
                \array_filter(
                    [
                        \implode(' ', \array_filter(
                            [
                                $this->renderValues(),
                                $this->renderLabel(),
                            ],
                            fn (?string $v): bool => $v !== null,
                        )),
                        \implode(' ', \array_filter(
                            [
                                $this->renderKillRatio(),
                            ],
                            fn (?string $v): bool => $v !== null,
                        )),
                    ],
                    fn (string $v): bool => $v !== '',
                ),
            )),
            ['id' => $this->id],
        );
    }

    public function renderValues(): ?string
    {
        $kHtml = $this->renderKDValue($this->kill, Yii::t('app', 'Kills'));
        $dHtml = $this->renderKDValue($this->death, Yii::t('app', 'Deaths'));

        if ($this->kill === null) {
            if ($this->assist === null && $this->kill_or_assist === null) {
                $kPart = $kHtml; // should be "?"
            } else {
                // only "K+A" known
                $kPart = vsprintf('《%s》', [
                    $this->renderKDValue($this->kill_or_assist, Yii::t('app', 'Kill or Assist')),
                ]);
            }
        } elseif ($this->assist === null) {
            $kPart = $kHtml;
        } else {
            $kPart = \vsprintf('%s %s', [
                $kHtml,
                Html::tag(
                    'small',
                    \vsprintf('+ %s', [
                        $this->renderKDValue($this->assist, Yii::t('app', 'Assists')),
                    ]),
                    ['class' => 'text-muted']
                ),
            ]);

            // $kPart = vsprintf('%s (+ %s)', [
            //     $kHtml,
            //     $this->renderKDValue($this->assist, Yii::t('app', 'Assists')),
            // ]);
        }

        return vsprintf('%s / %s', [
            $kPart,
            $dHtml,
        ]);
    }

    private function renderKDValue(?int $value, string $label): string
    {
        return Html::tag(
            'span',
            $value !== null
                ? $this->formatter->asInteger($value)
                : $this->formatter->asText('?'),
            [
                'class' => 'auto-tooltip',
                'title' => $label,
            ]
        );
    }

    public function renderLabel(): ?string
    {
        if ($this->kill === null || $this->death === null) {
            return null;
        }

        if ($this->kill > $this->death) {
            return Label::widget([
                'content' => '>',
                'color' => 'success',
                'formatter' => $this->formatter,
            ]);
        } elseif ($this->kill < $this->death) {
            return Label::widget([
                'content' => '<',
                'color' => 'danger',
                'formatter' => $this->formatter,
            ]);
        } else {
            return Label::widget([
                'content' => '=',
                'color' => 'default',
                'formatter' => $this->formatter,
            ]);
        }
    }

    public function renderKillRatio(): ?string
    {
        if ($this->kill === null || $this->death === null) {
            return null;
        }

        return Html::tag(
            'span',
            $this->formatter->asText(vsprintf('%s: %s', [
                Yii::t('app', 'Kill Ratio'),
                $this->formatter->asDecimal($this->killRatio, 2),
            ])),
            [
                'class' => 'auto-tooltip',
                'title' => vsprintf('%s: %s', [
                    Yii::t('app', 'Kill Rate'),
                    ($this->kill === 0 && $this->death === 0)
                        ? Yii::t('app', 'N/A')
                        : $this->formatter->asPercent(
                            $this->kill / ($this->kill + $this->death),
                            2
                        ),
                ]),
            ]
        );
    }

    public function getKillRatio(): ?float
    {
        if ($this->kill === null || $this->death === null) {
            return null;
        }

        if ($this->death === 0) {
            return ($this->kill === 0)
                ? 1.00
                : 99.99;
        }

        return $this->kill / $this->death;
    }
}
