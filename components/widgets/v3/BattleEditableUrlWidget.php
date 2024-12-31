<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use LogicException;
use Yii;
use app\assets\BattleEditAsset;
use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Salmon3;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

use function implode;
use function trim;

final class BattleEditableUrlWidget extends Widget
{
    public Battle3|Salmon3|null $model = null;

    public function run(): ?string
    {
        if (!$model = $this->model) {
            return null;
        }

        $disp = $this->renderDisplay($model);
        if ($disp === null) {
            return null;
        }

        return implode('', [
            $disp,
            $this->renderInput($model),
        ]);
    }

    private function renderDisplay(Battle3|Salmon3 $model): ?string
    {
        $currentValue = trim((string)$model->link_url);
        $isEditable = $this->isEditable($model);

        if ($currentValue === '' && !$isEditable) {
            return null;
        }

        if ($isEditable) {
            $view = $this->view;
            if ($view instanceof View) {
                BattleEditAsset::register($view);
            }
        }

        return Html::tag(
            'div',
            implode(' ', [
                $currentValue !== ''
                    ? Yii::$app->formatter->asUrl($currentValue, [
                        'rel' => 'nofollow noopener',
                        'target' => '_blank',
                    ])
                    : '',
                $isEditable
                    ? Html::button(
                        implode(' ', [
                            Icon::edit(),
                            Html::encode(Yii::t('app', 'Edit')),
                        ]),
                        [
                            'class' => 'btn btn-default btn-xs',
                            'disabled' => true,
                            'id' => 'link-cell-start-edit',
                        ],
                    )
                    : '',
            ]),
            [
                'id' => 'link-cell-display',
                'data' => [
                    'post' => match (true) {
                        $model instanceof Battle3 => Url::to(
                            ['api-internal/patch-battle3-url',
                                'id' => $model->uuid,
                            ],
                        ),
                        $model instanceof Salmon3 => Url::to(
                            ['api-internal/patch-salmon3-url',
                                'id' => $model->uuid,
                            ],
                        ),
                        default => throw new LogicException(),
                    },
                    'url' => $currentValue,
                ],
            ],
        );
    }

    private function renderInput(Battle3|Salmon3 $model): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::input(
                        type: 'url',
                        name: null,
                        value: '',
                        options: [
                            'class' => 'form-control',
                            'id' => 'link-cell-edit-input',
                            'placeholder' => 'https://www.youtube.com/watch?v=...',
                        ],
                    ),
                    ['class' => 'form-group-sm mb-1'],
                ),
                Html::button(
                    Html::encode(Yii::t('app', 'Apply')),
                    [
                        'class' => 'btn btn-primary btn-xs',
                        'data' => [
                            'error' => Yii::t('app', 'Could not be updated.'),
                        ],
                        'disabled' => null,
                        'id' => 'link-cell-edit-apply',
                    ],
                ),
            ]),
            [
                'id' => 'link-cell-edit',
                'style' => [
                    'display' => 'none',
                ],
            ],
        );
    }

    private function isEditable(Battle3|Salmon3 $model): bool
    {
        $loggedInUser = Yii::$app->user->identity;
        if (!$loggedInUser) {
            return false;
        }

        return (int)$loggedInUser->id === $model->user_id;
    }
}
