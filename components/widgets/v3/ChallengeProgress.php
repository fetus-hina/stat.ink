<?php

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\FontAwesomeAsset;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\web\View;

final class ChallengeProgress extends Widget
{
    public int $win = 0;
    public int $lose = 0;
    public bool $isRankUpBattle = false;

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            FontAwesomeAsset::register($view);
        }

        $maxWin = $this->isRankUpBattle ? 3 : 5;
        $win = \min(\max($this->win, 0), $maxWin);
        $lose = \min(\max($this->lose, 0), 3);

        return Html::tag(
            'div',
            implode('', [
                $this->renderCounter($win, $lose),
                $view instanceof View ? $this->renderWin($win, $maxWin) : '',
                $view instanceof View ? $this->renderLose($lose, 3) : '',
            ]),
            ['id' => $this->id]
        );
    }

    private function renderCounter(int $win, int $lose): string
    {
        $f = Yii::$app->formatter;

        return Html::tag(
            'div',
            Html::encode(
                \vsprintf('%s - %s', [
                    $f->asInteger($win),
                    $f->asInteger($lose),
                ])
            ),
            []
        );
    }

    private function renderWin(int $win, int $max): string
    {
        return Html::tag(
            'div',
            \implode('', [
                Html::tag(
                    'span',
                    \str_repeat(
                        Html::tag('span', '', ['class' => 'fas fa-fw fa-circle']),
                        $win
                    ),
                    ['class' => 'text-success']
                ),
                Html::tag(
                    'span',
                    \str_repeat(
                        Html::tag('span', '', ['class' => 'fas fa-fw fa-circle']),
                        $max - $win
                    ),
                    [
                        'class' => 'text-muted',
                        'style' => [
                            'opacity' => '0.2',
                        ],
                    ]
                ),
            ]),
            ['class' => 'series-progress'],
        );
    }

    private function renderLose(int $lose, int $max): string
    {
        return Html::tag(
            'div',
            \implode('', [
                Html::tag(
                    'span',
                    \str_repeat(
                        Html::tag('span', '', ['class' => 'fas fa-fw fa-square']),
                        $max - $lose
                    ),
                    ['class' => 'text-warning']
                ),
                Html::tag(
                    'span',
                    \str_repeat(
                        Html::tag('span', '', ['class' => 'fas fa-fw fa-times']),
                        $lose
                    ),
                    [
                        'class' => 'text-danger',
                        'style' => [
                            'opacity' => '0.5',
                        ],
                    ]
                ),
            ]),
            ['class' => 'series-progress'],
        );
    }
}
