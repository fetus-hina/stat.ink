<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
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
use yii\web\View;
use yii\web\YiiAsset;

use function implode;
use function vsprintf;

final class ModalFooter extends Widget
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
                $this->renderCloseButton(),
                $this->renderDeleteButton($model),
            ]),
            ['class' => 'modal-footer'],
        );
    }

    private function renderCloseButton(): string
    {
        return Html::button(
            vsprintf('%s %s', [
                Icon::close(),
                Html::encode(Yii::t('app', 'Close')),
            ]),
            [
                'class' => 'btn btn-default',
                'data' => ['dismiss' => 'modal'],
            ],
        );
    }

    private function renderDeleteButton(Battle3|Salmon3 $model): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            YiiAsset::register($view);
        }

        return Html::a(
            vsprintf('%s %s', [
                Icon::delete(),
                Html::encode(Yii::t('app', 'Delete')),
            ]),
            match (true) {
                $model instanceof Battle3 => ['show-v3/delete-battle',
                    'screen_name' => $model->user->screen_name,
                    'battle' => $model->uuid,
                ],
                $model instanceof Salmon3 => ['salmon-v3/delete',
                    'screen_name' => $model->user->screen_name,
                    'battle' => $model->uuid,
                ],
                default => throw new LogicException(),
            },
            [
                'class' => 'btn btn-danger',
                'data' => ['method' => 'DELETE'],
                'rel' => 'nofollow',
            ],
        );
    }
}
