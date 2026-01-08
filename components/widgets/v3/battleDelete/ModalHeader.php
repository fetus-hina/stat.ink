<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\battleDelete;

use LogicException;
use Yii;
use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Salmon3;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class ModalHeader extends Widget
{
    public Battle3|Salmon3|null $model = null;

    public function run(): string
    {
        if (!$model = $this->model) {
            throw new LogicException();
        }

        return Html::tag(
            'div',
            implode('', [
                $this->renderButton(),
                $this->renderTitle($model),
            ]),
            ['class' => 'modal-header'],
        );
    }

    private function renderButton(): string
    {
        return Html::button(
            Icon::close(),
            [
                'aria' => ['label' => Yii::t('app', 'Close')],
                'class' => 'close',
                'data' => ['dismiss' => 'modal'],
            ],
        );
    }

    private function renderTitle(Battle3|Salmon3 $model): string
    {
        return Html::tag(
            'h4',
            Html::encode(
                match (true) {
                    $model instanceof Battle3 => Yii::t('app', 'Delete This Battle'),
                    $model instanceof Salmon3 => Yii::t('app', 'Delete This Job'),
                    default => throw new LogicException(),
                },
            ),
            ['class' => 'modal-title'],
        );
    }
}
