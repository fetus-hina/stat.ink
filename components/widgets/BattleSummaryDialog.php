<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\AppAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function array_keys;
use function array_map;
use function array_values;
use function implode;

class BattleSummaryDialog extends Widget
{
    public function run()
    {
        return Html::tag(
            'div',
            $this->renderDialog(),
            [
                'class' => ['modal', 'fade'],
                'id' => $this->id,
                'role' => 'dialog',
                'tabindex' => '-1',
            ],
        );
    }

    private function renderDialog(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    $this->renderHeader(),
                    $this->renderBody(),
                ]),
                ['class' => 'modal-content'],
            ),
            [
                'class' => 'modal-dialog',
                'role' => 'document',
            ],
        );
    }

    private function renderHeader(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderCloseButton(),
                $this->renderHeaderTitle(),
            ]),
            ['class' => 'modal-header'],
        );
    }

    private function renderCloseButton(): string
    {
        return Html::button(
            Icon::close(),
            [
                'type' => 'button',
                'class' => 'close',
                'data' => [
                    'dismiss' => 'modal',
                ],
                'aria-label' => Yii::t('app', 'Close'),
            ],
        );
    }

    private function renderHeaderTitle(): string
    {
        return Html::tag('h4', Html::encode('title'), [
            'class' => 'modal-title',
        ]);
    }

    private function renderBody(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    Html::tag(
                        'div',
                        implode('', [
                            $this->renderPlotArea(),
                            $this->renderDataArea(),
                        ]),
                        ['class' => 'col-12 col-xs-12 col-md-7'],
                    ),
                    Html::tag(
                        'div',
                        $this->renderLegend(),
                        ['class' => 'col-12 col-xs-12 col-md-5'],
                    ),
                ]),
                ['class' => 'row'],
            ),
            ['class' => 'modal-body'],
        );
    }

    private function renderPlotArea(): string
    {
        return Html::tag('div', '', [
            'class' => 'box-plot w-100',
            'style' => [
                'height' => '240px',
            ],
        ]);
    }

    private function renderDataArea(): string
    {
        $data = [
            'max' => Html::encode(Yii::t('app', 'Maximum')),
            'pct95' => Html::encode(Yii::t('app', '{percentile} Percentile', [
                'percentile' => '95',
            ])),
            'q3' => Yii::t('app', 'Q<sub>3/4</sub>'),
            'q2' => Html::encode(Yii::t('app', 'Median')),
            'q1' => Yii::t('app', 'Q<sub>1/4</sub>'),
            'pct5' => Html::encode(Yii::t('app', '{percentile} Percentile', [
                'percentile' => '5',
            ])),
            'min' => Html::encode(Yii::t('app', 'Minimum')),
            'iqr' => Html::encode(Yii::t('app', 'IQR')),
            'avg' => Html::encode(Yii::t('app', 'Average')),
            'stddev' => Html::encode(Yii::t('app', 'Std Dev')),
        ];

        return Html::tag(
            'table',
            Html::tag('tbody', implode('', array_map(
                fn (string $key, string $label): string => Html::tag('tr', implode('', [
                    Html::tag('td', "{$label} :", ['class' => 'text-right w-50']),
                    Html::tag('td', '', ['data-key' => $key]),
                ])),
                array_keys($data),
                array_values($data),
            ))),
            [
                'class' => [
                    'table',
                    'table-striped',
                    'table-hover',
                    'mx-auto',
                    'my-3',
                ],
            ],
        );
    }

    private function renderLegend(): string
    {
        return Html::tag('p', implode('<br>', [
            Html::encode(Yii::t('app', 'Legends')) . ':',
            Html::img(
                Yii::$app->assetManager->getAssetUrl(
                    Yii::$app->assetManager->getBundle(AppAsset::class),
                    'summary-legends.png',
                ),
                [
                    'style' => [
                        'width' => '100%',
                        'max-width' => '226px',
                        'height' => 'auto',
                    ],
                ],
            ),
        ]));
    }
}
