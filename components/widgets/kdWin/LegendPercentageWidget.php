<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\kdWin;

use Yii;
use app\assets\EntireKDWinAsset;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

class LegendPercentageWidget extends Widget
{
    public $maxPct = 90;
    public $minPct = 10;
    public $numCells = 7;

    public function run()
    {
        BootstrapAsset::register($this->view);
        EntireKDWinAsset::register($this->view);

        return Html::tag(
            'div',
            $this->renderTable(),
            [
                'id' => $this->id,
                'class' => 'table-responsive',
            ],
        );
    }

    private function renderTable(): string
    {
        return Html::tag(
            'table',
            Html::tag(
                'tbody',
                implode('', array_map(
                    function (int $i): string {
                        return $this->renderRow($i);
                    },
                    range(0, $this->numCells - 1),
                )),
            ),
            ['class' => [
                'table',
                'table-bordered',
                'table-condensed',
                'rule-table',
            ]],
        );
    }

    private function renderRow(int $rowNumber): string
    {
        $step = ($this->maxPct - $this->minPct) / ($this->numCells - 1);
        $pct = $this->maxPct - $step * $rowNumber;
        return Html::tag(
            'tr',
            implode('', [
                Html::tag('td', '', [
                    'class' => [
                        'text-center',
                        'kdcell',
                        'percent-cell',
                    ],
                    'data' => [
                        'battle' => 1,
                        'percent' => $pct,
                    ],
                ]),
                Html::tag(
                    'td',
                    Html::encode(
                        ($rowNumber === 0 || $rowNumber === $this->numCells - 1 || $pct % 10 === 0)
                            ? implode('', [
                                Yii::$app->formatter->asPercent($pct / 100, 0),
                                ($rowNumber === 0) ? '+' : '',
                                ($rowNumber === $this->numCells - 1) ? '-' : '',
                            ])
                            : '⋮',
                    ),
                    ['class' => [
                        'text-center',
                        'kdcell',
                    ]],
                ),
            ]),
        );
    }
}
