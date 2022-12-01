<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use app\components\widgets\FA;
use yii\helpers\Html;

final class XProgress extends ChallengeProgress
{
    protected function getMaxWin(): int
    {
        return 5;
    }

    protected function getMaxLose(): int
    {
        return 5;
    }

    protected function renderWin(int $win, int $max): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'span',
                \str_repeat(
                    (string)FA::fas('circle')->fw(),
                    $win
                ),
                ['class' => 'text-success']
            ),
            ['class' => 'series-progress'],
        );
    }

    protected function renderLose(int $lose, int $max): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'span',
                \str_repeat(
                    (string)FA::fas('times')->fw(),
                    $lose
                ),
                [
                    'class' => 'text-danger',
                    'style' => [
                        'opacity' => '0.5',
                    ],
                ]
            ),
            ['class' => 'series-progress'],
        );
    }
}
