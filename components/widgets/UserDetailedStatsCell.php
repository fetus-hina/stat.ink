<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\AutoTooltipAsset;
use app\assets\UserDetailedStatsCellAsset;
use yii\base\Widget;
use yii\helpers\Html;

class UserDetailedStatsCell extends Widget
{
    public $rule;
    public $data;
    private $f;

    public function init()
    {
        parent::init();

        $this->f = Yii::$app->formatter;
    }

    public function run()
    {
        UserDetailedStatsCellAsset::register($this->view);

        $map = [
            'Win' => $this->renderWin(),
            'Lose' => $this->renderLose(),
            'Win %' => $this->renderWinPct(),
            'Kills' => $this->renderKill(),
            'Deaths' => $this->renderDeath(),
            'K/D' => $this->renderKD(),
            'Inked' => $this->renderInked(),
            'Max Inked' => $this->renderMaxInked(),
            'Kills/min' => $this->renderKillPerMin(),
            'Deaths/min' => $this->renderDeathPerMin(),
        ];

        return implode('<br>', array_filter(array_map(
            function (string $enLabel, ?string $content): ?string {
                if (!$content) {
                    return null;
                }
                return vsprintf('%s: %s', [
                    Html::encode(Yii::t('app', $enLabel)),
                    $content,
                ]);
            },
            array_keys($map),
            array_values($map),
        )));
    }

    public function renderWin(): string
    {
        return implode(' ', array_filter([
            Html::tag('span', Html::encode($this->f->asInteger($this->data->win)), [
                'class' => 'positive',
            ]),
            $this->data->win_ko > 0 && $this->data->win_to > 0
                ? vsprintf('%s: %s', [
                    Html::encode(Yii::t('app', 'KO')),
                    Html::encode($this->f->asPercent(
                        $this->data->win_ko / ($this->data->win_ko + $this->data->win_to),
                        1,
                    )),
                ])
                : null,
        ]));
    }

    public function renderLose(): string
    {
        return implode(' ', array_filter([
            Html::tag('span', Html::encode($this->f->asInteger($this->data->lose)), [
                'class' => 'negative',
            ]),
            $this->data->lose_ko > 0 && $this->data->lose_to > 0
                ? vsprintf('%s: %s', [
                    Html::encode(Yii::t('app', 'KO')),
                    Html::encode($this->f->asPercent(
                        $this->data->lose_ko / ($this->data->lose_ko + $this->data->lose_to),
                        1,
                    )),
                ])
                : null,
        ]));
    }

    public function renderWinPct(): string
    {
        if ($this->data->win < 1 && $this->data->lose < 1) {
            return $this->na();
        }

        return Html::encode(
            $this->f->asPercent($this->data->win / ($this->data->win + $this->data->lose), 1),
        );
    }

    public function renderKill(): string
    {
        return implode(' ', array_filter([
            Html::tag('span', Html::encode($this->f->asInteger($this->data->kill_sum)), [
                'class' => 'positive',
            ]),
            $this->data->battles_kd > 0
                ? vsprintf('(%s: %s)', [
                    Html::encode(Yii::t('app', 'Avg.')),
                    Html::tag(
                        'span',
                        Html::encode($this->f->asDecimal(
                            $this->data->kill_sum / $this->data->battles_kd,
                            2,
                        )),
                        ['class' => 'positive'],
                    ),
                ])
                : null,
        ]));
    }

    public function renderDeath(): string
    {
        return implode(' ', array_filter([
            Html::tag('span', Html::encode($this->f->asInteger($this->data->death_sum)), [
                'class' => 'negative',
            ]),
            $this->data->battles_kd > 0
                ? vsprintf('(%s: %s)', [
                    Html::encode(Yii::t('app', 'Avg.')),
                    Html::tag(
                        'span',
                        Html::encode($this->f->asDecimal(
                            $this->data->death_sum / $this->data->battles_kd,
                            2,
                        )),
                        ['class' => 'negative'],
                    ),
                ])
                : null,
        ]));
    }

    public function renderKD(): string
    {
        if ($this->data->kill_sum < 1 && $this->data->death_sum < 1) {
            return $this->na();
        }

        if ($this->data->death_sum < 1) {
            $ratio = 99.99;
            $rate = 1.00;
        } else {
            $ratio = $this->data->kill_sum / $this->data->death_sum;
            $rate = $this->data->kill_sum / ($this->data->kill_sum + $this->data->death_sum);
        }

        AutoTooltipAsset::register($this->view);
        return vsprintf('%s (%s)', [
            Html::tag('span', Html::encode($this->f->asDecimal($ratio, 3)), [
                'class' => 'auto-tooltip',
                'title' => Yii::t('app', 'Kill Ratio'),
            ]),
            Html::tag('span', Html::encode($this->f->asPercent($rate, 1)), [
                'class' => 'auto-tooltip',
                'title' => Yii::t('app', 'Kill Rate'),
            ]),
        ]);
    }

    public function renderInked(): ?string
    {
        if ($this->rule !== 'nawabari') {
            return null;
        }

        AutoTooltipAsset::register($this->view);
        return implode(' ', [
            Html::tag('span', $this->f->asMetricPrefixed($this->data->point_sum, 2), [
                'class' => 'auto-tooltip',
                'title' => $this->f->asInteger($this->data->point_sum),
            ]),
            $this->data->battles_pt > 0
                ? vsprintf('(%s: %s)', [
                    Html::encode(Yii::t('app', 'Avg.')),
                    Html::encode($this->f->asDecimal(
                        $this->data->point_sum / $this->data->battles_pt,
                        1,
                    )),
                ])
                : null,
        ]);
    }

    public function renderMaxInked(): ?string
    {
        if ($this->rule !== 'nawabari') {
            return null;
        }

        return Html::encode($this->f->asInteger($this->data->point_max));
    }

    public function renderKillPerMin(): ?string
    {
        if ($this->rule === 'nawabari') {
            return null;
        }

        if (
            $this->data->battles_time < 1 ||
            $this->data->time_sum < 1 ||
            $this->data->battles_kd < 1
        ) {
            return $this->na();
        }

        return Html::tag(
            'span',
            Html::encode(
                $this->f->asDecimal($this->data->kill_sum * 60 / $this->data->time_sum, 2),
            ),
            ['class' => 'positive'],
        );
    }

    public function renderDeathPerMin(): ?string
    {
        if ($this->rule === 'nawabari') {
            return null;
        }

        if (
            $this->data->battles_time < 1 ||
            $this->data->time_sum < 1 ||
            $this->data->battles_kd < 1
        ) {
            return $this->na();
        }

        return Html::tag(
            'span',
            Html::encode(
                $this->f->asDecimal($this->data->death_sum * 60 / $this->data->time_sum, 2),
            ),
            ['class' => 'negative'],
        );
    }

    public function na(): string
    {
        return Html::tag('span', Html::encode(Yii::t('app', 'N/A')), ['class' => 'na']);
    }
}
