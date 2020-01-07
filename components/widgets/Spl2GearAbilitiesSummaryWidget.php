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
use app\models\Ability2;
use app\models\Special2;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\grid\Column;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Spl2GearAbilitiesSummaryWidget extends Widget
{
    public $headgear;
    public $clothing;
    public $shoes;

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
        if (!$summary = $this->getSummary()) {
            return null;
        }

        return Html::tag(
            'div',
            GridView::widget([
                'dataProvider' => Yii::createObject([
                    '__class' => ArrayDataProvider::class,
                    'allModels' => $summary,
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
                        'value' => function (array $row): string {
                            return implode('', [
                                Html::tag(
                                    'div',
                                    AbilityIcon::spl2($row['ability']->key, [
                                        'title' => Yii::t('app-ability2', $row['ability']->name),
                                        'class' => 'auto-tooltip',
                                        'style' => [
                                            'height' => '3em',
                                        ],
                                    ]),
                                    ['class' => 'visible-xs-block']
                                ),
                                Html::tag(
                                    'div',
                                    implode(' ', [
                                        AbilityIcon::spl2($row['ability']->key, [
                                            'style' => [
                                                'height' => '1.667em',
                                            ],
                                        ]),
                                        Html::encode(Yii::t('app-ability2', $row['ability']->name))
                                    ]),
                                    ['class' => 'hidden-xs']
                                ),
                            ]);
                        },
                    ],
                    [
                        'label' => Yii::t('app', '5.7 Fmt'),
                        'format' => 'raw',
                        'value' => function (array $row, $key, $index, Column $column): string {
                            if ($row['ability']->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            return Html::encode($column->grid->formatter->asDecimal(
                                $row['primary'] + $row['secondary'] * 0.3,
                                1
                            ));
                        },
                    ],
                    [
                        'label' => Yii::t('app', '3,9 Fmt'),
                        'format' => 'raw',
                        'value' => function (array $row, $key, $index, Column $column): string {
                            if ($row['ability']->primary_only) {
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
                                    $fmt->asInteger($row['primary']),
                                    $fmt->asInteger($row['secondary']),
                                ]
                            ));
                        },
                    ],
                    [
                        'label' => Yii::t('app', '57 Fmt'),
                        'format' => 'raw',
                        'value' => function (array $row, $key, $index, Column $column): string {
                            if ($row['ability']->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            return Html::encode($column->grid->formatter->asInteger(
                                $row['primary'] * 10 + $row['secondary'] * 3
                            ));
                        },
                    ],
                ],
            ]),
            ['class' => 'table-responsive']
        );
    }

    private function getSummary(): ?array
    {
        $specials = ArrayHelper::map(
            Special2::find()->all(),
            'key',
            function (Special2 $model): Special2 {
                return $model;
            }
        );

        $results = [];
        $addAbility = function (
            Ability2 $ability,
            bool $isPrimary,
            bool $haveDoubler = false
        ) use (&$results): void {
            if ($haveDoubler && $isPrimary) {
                return;
            }

            $key = $ability->key;
            if (!isset($results[$key])) {
                $results[$key] = [
                    'ability' => $ability,
                    'primary' => 0,
                    'secondary' => 0,
                ];
            }

            $results[$key][$isPrimary ? 'primary' : 'secondary'] += ($haveDoubler ? 2 : 1);
        };

        foreach ([$this->headgear, $this->clothing, $this->shoes] as $gear) {
            if (!$gear || !$gear->primaryAbility) {
                return null;
            }

            $haveDoubler = ($gear->primaryAbility->key === 'ability_doubler');
            $addAbility($gear->primaryAbility, true, $haveDoubler);
            foreach ($gear->secondaries as $secondary) {
                if ($secondary->ability) {
                    $addAbility($secondary->ability, false, $haveDoubler);
                }
            }
        }

        usort($results, function (array $a, array $b): int {
            // メインにしかつかないやつは後回し
            if ($a['ability']->primary_only !== $b['ability']->primary_only) {
                return $a['ability']->primary_only ? 1 : -1;
            }

            $aPower = $a['primary'] * 10 + $a['secondary'];
            $bPower = $b['primary'] * 10 + $b['secondary'];
            return $bPower <=> $aPower
                ?: $b['primary'] <=> $a['primary']
                ?: $b['secondary'] <=> $b['secondary']
                ?: strcmp($a['ability']->name, $b['ability']->name);
        });

        return $results;
    }
}
