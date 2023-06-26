<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
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

    public int $decimalLabel = 2;
    public int $decimalValue = 2;

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
                    'q1' => (float)$this->q1,
                    'q2' => (float)$this->median,
                    'q3' => (float)$this->q3,
                    'max' => (int)$this->max,
                    'pct5' => (float)$this->pct5,
                    'pct95' => (float)$this->pct95,
                    'avg' => $this->total / $this->battles,
                    'stddev' => isset($this->stddev) ? (float)$this->stddev : null,
                ]),
                'disp' => Json::encode([
                    'min' => Yii::$app->formatter->asInteger((int)$this->min),
                    'q1' => Yii::$app->formatter->asDecimal((float)$this->q1, $this->decimalValue),
                    'q2' => Yii::$app->formatter->asDecimal((float)$this->median, $this->decimalValue),
                    'q3' => Yii::$app->formatter->asDecimal((float)$this->q3, $this->decimalValue),
                    'max' => Yii::$app->formatter->asInteger((int)$this->max),
                    'pct5' => Yii::$app->formatter->asDecimal((float)$this->pct5, $this->decimalValue),
                    'pct95' => Yii::$app->formatter->asDecimal((float)$this->pct95, $this->decimalValue),
                    'avg' => Yii::$app->formatter->asDecimal(
                        $this->total / $this->battles,
                        $this->decimalValue + 1,
                    ),
                    'stddev' => $this->stddev
                        ? Yii::$app->formatter->asDecimal($this->stddev, $this->decimalValue + 1)
                        : null,
                    'iqr' => Yii::$app->formatter->asDecimal($this->q3 - $this->q1, $this->decimalValue),
                    'title' => $this->summary ?? null,
                ]),
            ],
        ]);
    }

    private function renderContent(): string
    {
        return Html::tag(
            'span',
            Html::encode(
                Yii::$app->formatter->asDecimal(
                    $this->total / $this->battles,
                    $this->decimalLabel,
                ),
            ),
            [
                'class' => 'auto-tooltip',
                'title' => Yii::t(
                    'app',
                    match (true) {
                        isset($this->median) && isset($this->stddev) => 'max={max} min={min} median={median} stddev={stddev}',
                        isset($this->median) => 'max={max} min={min} median={median}',
                        default => $this->tooltipText,
                    },
                    [
                        'battle' => $this->battles,
                        'max' => $this->max === null ? '?' : Yii::$app->formatter->asInteger($this->max),
                        'median' => $this->median === null
                            ? '?'
                            : Yii::$app->formatter->asDecimal(
                                $this->median,
                                $this->decimalValue > 0 ? $this->decimalValue - 1 : 0,
                            ),
                        'min' => $this->min === null ? '?' : Yii::$app->formatter->asInteger($this->min),
                        'number' => $this->total,
                        'stddev' => $this->stddev === null
                            ? '?'
                            : Yii::$app->formatter->asDecimal($this->stddev, $this->decimalValue + 1),
                    ],
                ),
            ],
        );
    }
}
