<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\SalmonBossesAsset;
use app\assets\Spl3SalmonidAsset;
use app\components\i18n\Formatter;
use app\models\Salmon3;
use app\models\SalmonBossAppearance3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter as BaseFormatter;
use yii\web\View;

final class SalmonBosses extends Widget
{
    public Salmon3 $job;

    public ?BaseFormatter $formatter = null;

    /**
     * @inheritdoc
     * @return void
     */
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
            SalmonBossesAsset::register($view);
        }
    }

    public function run(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'table',
                implode('', [
                    $this->renderHeader(),
                    $this->renderBody(),
                ]),
                [
                    'class' => [
                        'salmon-v3-bosses',
                        'table',
                        'table-bordered',
                        'table-striped',
                    ],
                ]
            ),
            [
                'class' => [
                    'table-responsive',
                ],
            ],
        );
    }

    private function renderHeader(): string
    {
        return Html::tag(
            'thead',
            Html::tag(
                'tr',
                \implode('', [
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon2', 'Boss Salmonid')),
                        [
                            'class' => 'text-center',
                            'scope' => 'col',
                        ],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon3', 'Defeated')),
                        [
                            'class' => 'text-center',
                            'scope' => 'col',
                        ],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon3', 'Appearances')),
                        [
                            'class' => 'text-center',
                            'scope' => 'col',
                        ],
                    ),
                    Html::tag(
                        'th',
                        '',
                        [
                            'scope' => 'col',
                        ],
                    ),
                ]),
            ),
        );
    }

    private function renderBody(): string
    {
        $data = SalmonBossAppearance3::find()
            ->with(['boss'])
            ->andWhere(['salmon_id' => $this->job->id])
            ->andWhere(['>', 'appearances', 0])
            ->orderBy([
                'appearances' => SORT_DESC,
                'defeated' => SORT_DESC,
                'defeated_by_me' => SORT_DESC,
                'boss_id' => SORT_ASC,
            ])
            ->all();

        $maxAppearances = \max(ArrayHelper::getColumn($data, 'appearances'));

        return Html::tag(
            'tbody',
            \implode(
                '',
                ArrayHelper::getColumn(
                    $data,
                    fn (SalmonBossAppearance3 $model): string => Html::tag(
                        'tr',
                        \implode('', [
                            $this->renderBossSalmonid($model),
                            $this->renderDefeated($model),
                            $this->renderAppearances($model),
                            $this->renderBar($model, $maxAppearances),
                        ]),
                    ),
                ),
            ),
        );
    }

    private function renderBossSalmonid(SalmonBossAppearance3 $model): string
    {
        $am = null;
        $asset = null;
        $view = $this->view;
        if ($view instanceof View) {
            $am = Yii::$app->assetManager;
            $asset = Spl3SalmonidAsset::register($view);
        }

        $iconHtml = $am && $asset
            ? Html::img(
                $am->getAssetUrl($asset, \sprintf('%s.png', $model->boss->key)),
                ['class' => 'basic-icon'],
            )
            : '';

        return Html::tag(
            'th',
            \vsprintf('%s %s', [
                $iconHtml,
                Html::encode(Yii::t('app-salmon-boss3', $model->boss->name)),
            ]),
            ['scope' => 'row'],
        );
    }

    private function renderDefeated(SalmonBossAppearance3 $model): string
    {
        return Html::tag(
            'td',
            $model->defeated_by_me > 0
                ? \vsprintf('%s %s', [
                    Html::encode($this->formatter->asInteger($model->defeated)),
                    Html::tag(
                        'small',
                        Html::encode(
                            \vsprintf('(%s)', [
                                $this->formatter->asInteger($model->defeated_by_me),
                            ]),
                        ),
                    ),
                ])
                : Html::encode($this->formatter->asInteger($model->defeated)),
            ['class' => 'text-right'],
        );
    }

    private function renderAppearances(SalmonBossAppearance3 $model): string
    {
        return Html::tag(
            'td',
            Html::encode($this->formatter->asInteger($model->appearances)),
            ['class' => 'text-right'],
        );
    }

    private function renderBar(SalmonBossAppearance3 $model, int $maxAppearances): string
    {
        $f = $this->formatter;
        if (
            $maxAppearances >= 1 &&
            \is_int($model->defeated_by_me) &&
            \is_int($model->defeated) &&
            \is_int($model->appearances) &&
            $model->appearances > 0
        ) {
            return Html::tag(
                'td',
                Progress::widget([
                    'bars' => [
                        [
                            'label' => $f->asInteger($model->defeated_by_me),
                            'options' => [
                                'class' => [
                                    'auto-tooltip',
                                    'progress-bar-success',
                                ],
                                'title' => Yii::t('app-salmon3', 'Defeated'),
                            ],
                            'percent' => 100 * $model->defeated_by_me / $model->appearances,
                        ],
                        [
                            'label' => $f->asInteger($model->defeated - $model->defeated_by_me),
                            'options' => [
                                'class' => [
                                    'auto-tooltip',
                                    'progress-bar-warning',
                                ],
                                'title' => Yii::t('app-salmon3', 'Defeated (others)'),
                            ],
                            'percent' => 100 * ($model->defeated - $model->defeated_by_me) / $model->appearances,
                        ],
                        [
                            'label' => $f->asInteger($model->appearances - $model->defeated),
                            'options' => [
                                'class' => [
                                    'auto-tooltip',
                                    'progress-bar-danger',
                                ],
                                'title' => Yii::t('app-salmon3', 'Not Defeated'),
                            ],
                            'percent' => 100 - 100 * $model->defeated / $model->appearances,
                        ],
                    ],
                    'options' => [
                        'style' => [
                            'width' => sprintf('%f%%', 100 * $model->appearances / $maxAppearances),
                        ],
                    ],
                ]),
                ['class' => 'text-left'],
            );
        }

        return Html::tag('td', '');
    }
}
