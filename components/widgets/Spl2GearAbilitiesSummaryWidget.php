<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\components\widgets\AbilityIcon;
use app\models\Ability2Info;
use app\models\Special2;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\grid\Column;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Spl2GearAbilitiesSummaryWidget extends Widget
{
    public $summary;

    public function run()
    {
        $table = $this->renderTable();
        if (!$table) {
            return '';
        }
        return Html::tag('div', $table, [
            'class' => 'mt-3',
        ]);
    }

    private function renderTable(): ?string
    {
        if (!$this->summary) {
            return null;
        }

        return Html::tag(
            'div',
            GridView::widget([
                'dataProvider' => Yii::createObject([
                    '__class' => ArrayDataProvider::class,
                    'allModels' => $this->summary,
                    'pagination' => false,
                    'sort' => false,
                ]),
                'layout' => '{items}',
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered table-condensed m-0',
                ],
                'columns' => [
                    [
                        'label' => Yii::t('app-gearstat', 'Gear Abilities'),
                        'format' => 'raw',
                        'value' => function (Ability2Info $model): string {
                            return implode('', [
                                Html::tag(
                                    'div',
                                    AbilityIcon::spl2($model->ability->key, [
                                        'title' => Yii::t('app-ability2', $model->ability->name),
                                        'class' => 'auto-tooltip',
                                        'style' => [
                                            'height' => '3em',
                                        ],
                                    ]),
                                    ['class' => 'hidden-lg']
                                ),
                                Html::tag(
                                    'div',
                                    implode(' ', [
                                        AbilityIcon::spl2($model->ability->key, [
                                            'style' => [
                                                'height' => '1.667em',
                                            ],
                                        ]),
                                        Html::encode(Yii::t('app-ability2', $model->ability->name))
                                    ]),
                                    ['class' => 'visible-lg-block']
                                ),
                            ]);
                        },
                    ],
                    [
                        'label' => Yii::t('app', '5.7 Fmt'),
                        'format' => 'raw',
                        'value' => function (
                            Ability2Info $model,
                            $key,
                            $index,
                            Column $column
                        ): string {
                            if ($model->ability->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            return Html::encode($column->grid->formatter->asDecimal(
                                $model->get57Format() / 10,
                                1
                            ));
                        },
                    ],
                    [
                        'label' => Yii::t('app', '3,9 Fmt'),
                        'format' => 'raw',
                        'value' => function (
                            Ability2Info $model,
                            $key,
                            $index,
                            Column $column
                        ): string {
                            if ($model->ability->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            $fmt = $column->grid->formatter;
                            $decimal = $fmt->asDecimal(0.5, 1);
                            return Html::encode(implode(
                                // 小数点が "." なら "," で区切り、そうで無ければ "+" で区切る
                                (strpos($decimal, '.') !== false) ? ', ' : ' + ',
                                [
                                    $fmt->asInteger($model->primary),
                                    $fmt->asInteger($model->secondary),
                                ]
                            ));
                        },
                    ],
                    [
                        'label' => Yii::t('app', '57 Fmt'),
                        'format' => 'raw',
                        'value' => function (
                            Ability2Info $model,
                            $key,
                            $index,
                            Column $column
                        ): string {
                            if ($model->ability->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            return Html::encode($column->grid->formatter->asInteger(
                                $model->get57Format()
                            ));
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Effects'),
                        'format' => 'ntext',
                        'value' => function (
                            Ability2Info $model,
                            $key,
                            $index,
                            Column $column
                        ): string {
                            return (string)$model->coefficient;
                        }
                    ],
                ],
            ]),
            ['class' => 'table-responsive table-responsive-force nobr']
        );
    }
}
