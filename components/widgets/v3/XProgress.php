<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use app\components\widgets\Icon;
use yii\helpers\Html;

use function str_repeat;

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
            str_repeat(
                Icon::s3ChallengeProgressWin(),
                $win,
            ),
            ['class' => 'series-progress'],
        );
    }

    protected function renderLose(int $lose, int $max): string
    {
        return Html::tag(
            'div',
            str_repeat(
                Icon::s3ChallengeProgressLose(),
                $lose,
            ),
            ['class' => 'series-progress'],
        );
    }
}
