<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use LogicException;
use Yii;
use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Salmon3;
use app\models\User;
use yii\base\Widget;
use yii\helpers\Html;

final class BattlePrevNext extends Widget
{
    public ?User $user = null;

    /**
     * @var Battle3|Salmon3|null
     */
    public $prevBattle = null;

    /**
     * @var Battle3|Salmon3|null
     */
    public $nextBattle = null;

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
                \implode(' ', [
                    Icon::prevPage(),
                    $this->generatePrevLabel($this->prevBattle),
                ]),
                $this->generateUrl($this->prevBattle),
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
                \implode(' ', [
                    $this->generateNextLabel($this->nextBattle),
                    Icon::nextPage(),
                ]),
                $this->generateUrl($this->nextBattle),
                ['class' => 'btn btn-default']
            ),
            ['class' => 'col-xs-6 pull-right text-right']
        );
    }

    /**
     * @param Battle3|Salmon3 $model
     */
    private function generateNextLabel($model): string
    {
        switch (\get_class($model)) {
            case Battle3::class:
                return Html::encode(Yii::t('app', 'Next Battle'));

            case Salmon3::class:
                return Html::encode(Yii::t('app-salmon2', 'Next Job'));

            default:
                throw new LogicException();
        }
    }

    /**
     * @param Battle3|Salmon3 $model
     */
    private function generatePrevLabel($model): string
    {
        switch (\get_class($model)) {
            case Battle3::class:
                return Html::encode(Yii::t('app', 'Prev. Battle'));

            case Salmon3::class:
                return Html::encode(Yii::t('app-salmon2', 'Prev. Job'));

            default:
                throw new LogicException();
        }
    }

    /**
     * @param Battle3|Salmon3 $model
     */
    private function generateUrl($model): array
    {
        switch (\get_class($model)) {
            case Battle3::class:
                return ['/show-v3/battle',
                    'screen_name' => $this->user->screen_name,
                    'battle' => $model->uuid,
                ];

            case Salmon3::class:
                return ['/salmon-v3/view',
                    'screen_name' => $this->user->screen_name,
                    'battle' => $model->uuid,
                ];

            default:
                throw new LogicException();
        }
    }
}
