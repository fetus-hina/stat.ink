<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\battleDelete;

use LogicException;
use Yii;
use app\models\Battle3;
use app\models\Salmon3;
use app\models\User;
use yii\base\Widget;
use yii\helpers\Html;

final class Modal extends Widget
{
    public Battle3|Salmon3|null $model = null;

    public function run(): string
    {
        if (!$model = $this->model) {
            throw new LogicException();
        }

        return Html::tag(
            'div',
            $this->renderDialog($model),
            [
                'class' => [
                    'fade',
                    'modal',
                    'text-left',
                ],
                'id' => (string)$this->id,
                'role' => 'dialog',
                'tabindex' => '-1',
            ],
        );
    }

    private function renderDialog(Battle3|Salmon3 $model): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                \implode('', [
                    ModalHeader::widget(['model' => $model]),
                    ModalBody::widget(['model' => $model]),
                    ModalFooter::widget(['model' => $model]),
                ]),
                ['class' => 'modal-content'],
            ),
            ['class' => 'modal-dialog', 'role' => 'document'],
        );
    }
}
