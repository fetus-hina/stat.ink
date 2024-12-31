<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\kdWin;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;

class LegendWidget extends Widget
{
    public $id = 'kdwin-legend';

    public function run()
    {
        return implode('', [
            Html::tag(
                'h3',
                Html::encode(Yii::t('app', 'Legend')),
                ['id' => $this->id . '-legend'],
            ),
            Html::tag('div', implode('', [
                LegendPercentageWidget::widget(['id' => $this->id . '-pct']),
                LegendPopulationWidget::widget(['id' => $this->id . '-pop']),
            ]), ['id' => $this->id]),
        ]);
    }
}
