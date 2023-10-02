<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\Spl3ChallengeProgressIconAsset;
use app\components\widgets\Icon;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function implode;
use function max;
use function min;
use function str_repeat;
use function vsprintf;

class ChallengeProgress extends Widget
{
    public int $win = 0;
    public int $lose = 0;
    public bool $isRankUpBattle = false;

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            Spl3ChallengeProgressIconAsset::register($view);
        }

        $maxWin = $this->getMaxWin();
        $maxLose = $this->getMaxLose();
        $win = $this->getNormalizedWin();
        $lose = $this->getNormalizedLose();

        return Html::tag(
            'div',
            implode('', [
                $this->renderCounter($win, $lose),
                $view instanceof View ? $this->renderWin($win, $maxWin) : '',
                $view instanceof View ? $this->renderLose($lose, $maxLose) : '',
            ]),
            ['id' => $this->id],
        );
    }

    protected function getNormalizedWin(): int
    {
        $maxWin = $this->getMaxWin();
        return min(max($this->win, 0), $maxWin);
    }

    protected function getNormalizedLose(): int
    {
        $maxLose = $this->getMaxLose();
        return min(max($this->lose, 0), $maxLose);
    }

    protected function getMaxWin(): int
    {
        return $this->isRankUpBattle ? 3 : 5;
    }

    protected function getMaxLose(): int
    {
        return 3;
    }

    protected function renderCounter(int $win, int $lose): string
    {
        $f = Yii::$app->formatter;

        return Html::tag(
            'div',
            Html::encode(
                vsprintf('%s - %s', [
                    $f->asInteger($win),
                    $f->asInteger($lose),
                ]),
            ),
            [],
        );
    }

    protected function renderWin(int $win, int $max): string
    {
        return Html::tag(
            'div',
            implode('', [
                str_repeat(
                    Icon::s3ChallengeProgressWin(),
                    $win,
                ),
                str_repeat(
                    Icon::s3ChallengeProgressWinPotential(),
                    $max - $win,
                ),
            ]),
            ['class' => 'series-progress'],
        );
    }

    protected function renderLose(int $lose, int $max): string
    {
        return Html::tag(
            'div',
            implode('', [
                str_repeat(
                    Icon::s3ChallengeProgressLosePotential(),
                    $max - $lose,
                ),
                str_repeat(
                    Icon::s3ChallengeProgressLose(),
                    $lose,
                ),
            ]),
            ['class' => 'series-progress'],
        );
    }
}
