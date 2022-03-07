<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\grid;

use Yii;
use app\components\helpers\Html;
use yii\grid\Column;

class SalmonActionColumn extends Column
{
    public $user;

    protected function renderDataCellContent($model, $key, $index)
    {
        $user = $this->user ?: $model->user;

        return implode(' ', array_filter(
            [
                Html::a(
                    Html::encode(Yii::t('app', 'Detail')),
                    ['salmon/view',
                        'screen_name' => $user->screen_name ?? '_',
                        'id' => $model->id,
                    ],
                    [
                        'class' => 'btn btn-primary btn-xs',
                    ]
                ),
                //TODO: video link
            ],
            fn (?string $content): bool => $content !== null && $content !== ''
        ));
    }
}
