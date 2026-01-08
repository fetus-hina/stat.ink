<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\GearAbilityNumberSwitcherAsset;
use app\assets\Spl2WeaponAsset;
use app\models\Ability2Info;
use yii\base\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\data\ArrayDataProvider;
use yii\grid\Column;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

use function implode;
use function sprintf;
use function vsprintf;

class Spl2GearAbilitiesSummaryWidget extends Widget
{
    private static $serial = 0;
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
                            $items = [];
                            $items[] = Html::tag(
                                'div',
                                AbilityIcon::spl2($model->ability->key, [
                                    'title' => Yii::t('app-ability2', $model->ability->name),
                                    'class' => 'auto-tooltip',
                                    'style' => [
                                        'height' => '3em',
                                    ],
                                ]),
                                ['class' => 'hidden-lg'],
                            );
                            $items[] = Html::tag(
                                'div',
                                implode(' ', [
                                    AbilityIcon::spl2($model->ability->key, [
                                        'style' => [
                                            'height' => '1.667em',
                                        ],
                                    ]),
                                    Html::encode(Yii::t('app-ability2', $model->ability->name)),
                                ]),
                                ['class' => 'visible-lg-block'],
                            );
                            if (
                                $model->ability->key === 'special_power_up' &&
                                $model->weapon &&
                                $model->weapon->special
                            ) {
                                $sp = $model->weapon->special;
                                $icons = Spl2WeaponAsset::register($this->view);
                                $items[] = Html::tag(
                                    'div',
                                    implode('', [
                                        Html::img(
                                            $icons->getIconUrl('sp/' . $sp->key),
                                            [
                                                'style' => [
                                                    'height' => '2em',
                                                ],
                                                'title' => Yii::t('app-special2', $sp->name),
                                                'class' => 'auto-tooltip',
                                            ],
                                        ),
                                    ]),
                                    ['class' => 'hidden-lg pl-2'],
                                );
                                $items[] = Html::tag(
                                    'div',
                                    implode('', [
                                        Html::img(
                                            $icons->getIconUrl('sp/' . $sp->key),
                                            ['style' => [
                                                'height' => '1.333em',
                                            ],
                                            ],
                                        ),
                                        Html::encode(Yii::t('app-special2', $sp->name)),
                                    ]),
                                    ['class' => 'visible-lg-block pl-4'],
                                );
                            }

                            return implode('', $items);
                        },
                    ],
                    [
                        'label' => ButtonDropdown::widget([
                            'label' => '#',
                            'options' => [
                                'class' => 'btn btn-xs btn-default',
                            ],
                            'dropdown' => [
                                'items' => [
                                    [
                                        'label' => Yii::t('app', '{decimal5_7} Format', [
                                            'decimal5_7' => Yii::$app->formatter->asDecimal(5.7, 1),
                                        ]),
                                        'url' => '#',
                                        'linkOptions' => [
                                            'class' => sprintf('%s-fmt-selector', $this->id),
                                            'data-format' => '5.7',
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('app', '57 Format'),
                                        'url' => '#',
                                        'linkOptions' => [
                                            'class' => sprintf('%s-fmt-selector', $this->id),
                                            'data-format' => '57',
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('app', '3, 9 Format'),
                                        'url' => '#',
                                        'linkOptions' => [
                                            'class' => sprintf('%s-fmt-selector', $this->id),
                                            'data-format' => '3,9',
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('app', '3+9 Format'),
                                        'url' => '#',
                                        'linkOptions' => [
                                            'class' => sprintf('%s-fmt-selector', $this->id),
                                            'data-format' => '3+9',
                                        ],
                                    ],
                                ],
                            ],
                        ]),
                        'encodeLabel' => false,
                        'format' => 'raw',
                        'contentOptions' => function (Ability2Info $model): array {
                            if ($model->ability->primary_only) {
                                return ['class' => 'text-center'];
                            }

                            return ['class' => 'text-right'];
                        },
                        'value' => function (Ability2Info $model): string {
                            if ($model->ability->primary_only) {
                                return Html::tag('span', (string)FA::fas('check'), [
                                    'class' => 'text-success',
                                ]);
                            }

                            if (!$gp = $model->get57Format()) {
                                return '';
                            }

                            $id = sprintf('%s-%d', $this->id, ++static::$serial);
                            $this->view->registerJs(sprintf(
                                'jQuery(%s).gearAbilityNumberSwitcher(%s);',
                                Json::encode(sprintf('.%s-fmt-selector', $this->id)),
                                sprintf('jQuery(%s)', Json::encode('#' . $id)),
                            ));
                            GearAbilityNumberSwitcherAsset::register($this->view);

                            $f = Yii::$app->formatter;
                            return Html::tag(
                                'span',
                                Html::encode($f->asDecimal($model->get57Format() / 10, 1)),
                                [
                                    'id' => $id,
                                    'data' => [
                                        'values' => Json::encode([
                                            '5.7' => $f->asDecimal($gp / 10, 1),
                                            '57' => $f->asInteger($gp),
                                            '3,9' => vsprintf('%d, %d', [
                                                $f->asInteger($model->primary),
                                                $f->asInteger($model->secondary),
                                            ]),
                                            '3+9' => vsprintf('%d + %d', [
                                                $f->asInteger($model->primary),
                                                $f->asInteger($model->secondary),
                                            ]),
                                        ]),
                                    ],
                                ],
                            );
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Effects'),
                        'format' => 'ntext',
                        'value' => fn (
                            Ability2Info $model,
                            $key,
                            $index,
                            Column $column,
                        ): string => (string)$model->coefficient,
                    ],
                ],
            ]),
            [
                'class' => 'table-responsive table-responsive-force nobr',
                'id' => $this->id,
            ],
        );
    }
}
