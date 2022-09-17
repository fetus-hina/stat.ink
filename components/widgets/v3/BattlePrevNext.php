<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\models\Battle3;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;

final class BattlePrevNext extends Widget
{
    public ?User $user = null;
    public ?Battle3 $prevBattle = null;
    public ?Battle3 $nextBattle = null;

    public function run(): string
    {
        if (
            !$this->user ||
            (!$this->prevBattle && !$this->nextBattle)
        ) {
            return '';
        }

        return Html::tag(
            'div',
            \implode('', [
                $this->renderPrev(),
                $this->renderNext(),
            ]),
            [
                'class' => 'row',
                'style' => [
                    'margin-bottom' => '15px',
                ],
            ]
        );
    }

    private function renderPrev(): string
    {
        if (!$this->prevBattle || !$this->user) {
            return '';
        }

        return Html::tag(
            'div',
            Html::a(
                \implode('', [
                    Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-left']),
                    Yii::t('app', 'Prev. Battle'),
                ]),
                ['/show-v3/battle',
                    'screen_name' => $this->user->screen_name,
                    'battle' => $this->prevBattle->uuid,
                ],
                ['class' => 'btn btn-default']
            ),
            ['class' => 'col-xs-6']
        );
    }

    private function renderNext(): string
    {
        if (!$this->nextBattle || !$this->user) {
            return '';
        }

        return Html::tag(
            'div',
            Html::a(
                \implode('', [
                    Yii::t('app', 'Next Battle'),
                    Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-right']),
                ]),
                ['/show-v3/battle',
                    'screen_name' => $this->user->screen_name,
                    'battle' => $this->nextBattle->uuid,
                ],
                ['class' => 'btn btn-default']
            ),
            ['class' => 'col-xs-6 pull-right text-right']
        );
    }
}
