<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\BattleSummaryDialogAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class BattleSummaryItemWidget extends Widget
{
    public $battles;
    public $total;
    public $min;
    public $max;
    public $q1;
    public $q3;
    public $median;
    public $pct5;
    public $pct95;
    public $stddev;
    public $tooltipText;
    public $summary;

    public function run(): string
    {
        if ($this->battles < 1) {
            return Html::encode(Yii::t('app', 'N/A'));
        }

        if (
            ($this->min === null) ||
            ($this->max === null) ||
            ($this->median === null) ||
            ($this->q1 === null) ||
            ($this->q3 === null) ||
            ($this->pct5 === null) ||
            ($this->pct95 === null)
        ) {
            return $this->renderContent();
        }

        BattleSummaryDialogAsset::register($this->view);
        return Html::a($this->renderContent(), null, [
            'class' => 'summary-box-plot text-link',
            'data' => [
                'stats' => Json::encode([
                    'min' => (int)$this->min,
                    'q1'  => (float)$this->q1,
                    'q2'  => (float)$this->median,
                    'q3'  => (float)$this->q3,
                    'max' => (int)$this->max,
                    'pct5' => (float)$this->pct5,
                    'pct95' => (float)$this->pct95,
                    'avg' => $this->total / $this->battles,
                    'stddev' => isset($this->stddev) ? (float)$this->stddev : null,
                ]),
                'disp' => Json::encode([
                    'min' => Yii::$app->formatter->asInteger((int)$this->min),
                    'q1'  => Yii::$app->formatter->asDecimal((float)$this->q1, 2),
                    'q2'  => Yii::$app->formatter->asDecimal((float)$this->median, 2),
                    'q3'  => Yii::$app->formatter->asDecimal((float)$this->q3, 2),
                    'max' => Yii::$app->formatter->asInteger((int)$this->max),
                    'pct5' => Yii::$app->formatter->asDecimal((float)$this->pct5, 2),
                    'pct95' => Yii::$app->formatter->asDecimal((float)$this->pct95, 2),
                    'avg' => Yii::$app->formatter->asDecimal($this->total / $this->battles, 2),
                    'stddev' => $this->stddev
                        ? Yii::$app->formatter->asDecimal($this->stddev, 3)
                        : null,
                    'iqr' => Yii::$app->formatter->asDecimal($this->q3 - $this->q1, 2),
                    'title' => $this->summary ?? null,
                ]),
            ],
        ]);
    }

    private function renderContent(): string
    {
        return Html::tag(
            'span',
            Html::encode(Yii::$app->formatter->asDecimal($this->total / $this->battles, 2)),
            [
                'class' => 'auto-tooltip',
                'title' => isset($this->median)
                    ? Yii::t('app', 'max={max} min={min} median={median}', [
                        'max' => $this->max === null
                            ? '?'
                            : Yii::$app->formatter->asInteger($this->max),
                        'min' => $this->min === null
                            ? '?'
                            : Yii::$app->formatter->asInteger($this->min),
                        'median' => $this->median === null
                            ? '?'
                            : Yii::$app->formatter->asDecimal($this->median, 1),
                    ])
                    : Yii::t('app', $this->tooltipText, [
                        'number' => $this->total,
                        'battle' => $this->battles,
                    ]),
            ],
        );
    }
}
