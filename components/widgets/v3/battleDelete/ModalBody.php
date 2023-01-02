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
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

final class ModalBody extends Widget
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
                Html::tag('p', Html::encode(Yii::t('app', 'You can delete this battle.'))),
                Html::tag(
                    'ul',
                    implode('', $this->getMessages()),
                    ['class' => 'mb-3'],
                ),
            ]),
            ['class' => 'modal-body'],
        );
    }

    private function getMessages(): array
    {
        return [
            Html::tag(
                'li',
                Html::encode(Yii::t('app', 'If you delete this battle, it will be gone forever.')),
                ['class' => 'mb-2'],
            ),
            Html::tag(
                'li',
                implode('<br>', [
                    Html::tag(
                        'strong',
                        Html::encode(Yii::t('app', 'Please do not use this feature to destroy evidence.')),
                        ['class' => 'text-danger'],
                    ),
                    Html::encode(
                        Yii::t('app', 'This option is provided for deleting an incorrectly-reported battle.'),
                    ),
                ]),
                ['class' => 'mb-2'],
            ),
            Html::tag(
                'li',
                Html::encode(Yii::t('app', 'If you misuse this feature, you will be banned.')),
                ['class' => 'mb-2'],
            ),
        ];
    }
}
