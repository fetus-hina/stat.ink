<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\SalmonWavesAsset;
use app\components\i18n\Formatter;
use app\models\SalmonWave2;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

use function array_filter;
use function array_map;
use function call_user_func;
use function implode;
use function is_callable;

final class SalmonWaves extends Widget
{
    public $work;

    public $wave1;
    public $wave2;
    public $wave3;

    public $formatter;

    public function init()
    {
        parent::init();

        if (!$this->formatter) {
            $this->formatter = Yii::createObject([
                'class' => Formatter::class,
                'nullDisplay' => '-',
            ]);
        }

        $view = $this->view;
        if ($view instanceof View) {
            BootstrapAsset::register($view);
            SalmonWavesAsset::register($view);
        }
    }

    public function run(): string
    {
        BootstrapAsset::register($this->view);

        return Html::tag(
            'div',
            Html::tag(
                'table',
                $this->renderHeader() . $this->renderBody(),
                [
                    'id' => $this->id,
                    'class' => [
                        'salmon-waves',
                        'table',
                        'table-bordered',
                        'table-striped',
                    ],
                ],
            ),
            ['class' => 'table-responsive'],
        );
    }

    protected function renderHeader(): string
    {
        return Html::tag('thead', Html::tag('tr', implode('', [
            Html::tag('th', ''),
            Html::tag('th', Html::encode(
                Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 1]),
            )),
            Html::tag('th', Html::encode(
                Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 2]),
            )),
            Html::tag('th', Html::encode(
                Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 3]),
            )),
            Html::tag('th', Html::encode(
                Yii::t('app', 'Total'),
            )),
        ])));
    }

    protected function renderBody(): string
    {
        $data = array_filter([
            $this->work && $this->work->clear_waves !== null
                ? [
                    'label' => '',
                    'format' => 'raw',
                    'total' => null,
                    'value' => function (SalmonWave2 $wave, int $waveNumber, self $widget): string {
                        $clearWaves = $widget->work->clear_waves;
                        if ($clearWaves >= $waveNumber) {
                            return Label::widget([
                                'content' => Yii::t('app-salmon2', '✓'),
                                'color' => 'success',
                                'formatter' => $widget->formatter,
                            ]);
                        } elseif ($clearWaves + 1 === $waveNumber) {
                            return Label::widget([
                                'content' => Yii::t('app-salmon2', '✘'),
                                'color' => 'danger',
                                'formatter' => $widget->formatter,
                            ]);
                        } else {
                            return '';
                        }
                    },
                ]
                : null,
            [
                'label' => Yii::t('app-salmon-event2', 'Event'),
                'format' => 'text',
                'total' => null,
                'value' => fn (SalmonWave2 $wave, int $waveNumber, self $widget): ?string => Yii::t('app-salmon-event2', $wave->event->name ?? null),
            ],
            [
                'label' => Yii::t('app-salmon-tide2', 'Water Level'),
                'format' => 'raw',
                'total' => null,
                'value' => function (SalmonWave2 $wave, int $waveNumber, self $widget): string {
                    $options = [
                        'low' => [
                            'width' => '33.333%',
                            'color' => 'info',
                        ],
                        'normal' => [
                            'width' => '66.667%',
                            'color' => 'success',
                        ],
                        'high' => [
                            'width' => '100%',
                            'color' => 'danger',
                        ],
                    ];

                    if (!isset($options[$wave->water->key])) {
                        return Html::encode(
                            Yii::t('app-salmon-tide2', $wave->water->name ?? null),
                        );
                    }

                    $opt = $options[$wave->water->key];
                    return Html::tag(
                        'div',
                        Html::tag(
                            'div',
                            Html::encode(Yii::t('app-salmon-tide2', $wave->water->name ?? null)),
                            [
                                'class' => [
                                    'progress-bar',
                                    "progress-bar-{$opt['color']}",
                                ],
                                'role' => 'progressbar',
                                'style' => [
                                    'width' => $opt['width'],
                                ],
                            ],
                        ),
                        [
                            'class' => 'progress',
                            'style' => [
                                'margin-bottom' => '0',
                            ],
                        ],
                    );
                },
            ],
            [
                'label' => Yii::t('app-salmon2', 'Quota'),
                'attribute' => 'golden_egg_quota',
                'format' => 'integer',
                'total' => '+',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Delivers'),
                'attribute' => 'golden_egg_delivered',
                'format' => 'integer',
                'total' => '+',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Appearances'),
                'attribute' => 'golden_egg_appearances',
                'format' => 'integer',
                'total' => '+',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Power Eggs'),
                'attribute' => 'power_egg_collected',
                'format' => 'integer',
                'total' => '+',
            ],
        ]);
        return Html::tag('tbody', implode('', array_map(
            fn (array $row): string => $this->renderRow($row),
            $data,
        )));
    }

    protected function renderRow(array $rowInfo): string
    {
        return Html::tag('tr', implode('', [
            $this->renderRowHeader($rowInfo),
            $this->renderCellData($rowInfo, $this->wave1, 1),
            $this->renderCellData($rowInfo, $this->wave2, 2),
            $this->renderCellData($rowInfo, $this->wave3, 3),
            $this->renderTotalData($rowInfo),
        ]));
    }

    protected function renderRowHeader(array $rowInfo): string
    {
        return Html::tag(
            'th',
            $this->formatter->asText($rowInfo['label']),
            ['scope' => 'row'],
        );
    }

    protected function renderCellData(array $rowInfo, ?SalmonWave2 $wave, int $waveNumber): string
    {
        return Html::tag(
            'td',
            $this->formatter->format(
                $this->renderCellValue($rowInfo, $wave, $waveNumber),
                ArrayHelper::getValue($rowInfo, 'format', 'text'),
            ),
        );
    }

    protected function renderCellValue(array $rowInfo, ?SalmonWave2 $wave, int $waveNumber)
    {
        if (!$wave) {
            return null;
        }

        $value = ArrayHelper::getValue($rowInfo, 'value');
        if ($value === null && isset($rowInfo['attribute'])) {
            $value = ArrayHelper::getValue($wave, $rowInfo['attribute']);
        }

        if (is_callable($value)) {
            $value = call_user_func($value, $wave, $waveNumber, $this);
        }

        return $value;
    }

    protected function renderTotalData(array $rowInfo): string
    {
        return Html::tag('td', $this->renderTotalDataValue($rowInfo));
    }

    protected function renderTotalDataValue(array $rowInfo)
    {
        $waves = [
            $this->wave1,
            $this->wave2,
            $this->wave3,
        ];
        $method = ArrayHelper::getValue($rowInfo, 'total');
        switch ($method) {
            case null:
            default:
                return '';

            case '+':
            case 'add':
                $total = 0;
                foreach ($waves as $i => $wave) {
                    if (!$wave) {
                        continue;
                    }
                    $value = ArrayHelper::getValue($rowInfo, 'value');
                    if ($value === null && isset($rowInfo['attribute'])) {
                        $value = ArrayHelper::getValue($wave, $rowInfo['attribute']);
                    }
                    if (is_callable($value)) {
                        $value = call_user_func($value, $wave, $i + 1, $this);
                    }
                    if ($value !== null) {
                        $total += $value;
                    }
                }
                return $this->formatter->format(
                    $total,
                    ArrayHelper::getValue($rowInfo, 'format', 'text'),
                );
        }
    }
}
