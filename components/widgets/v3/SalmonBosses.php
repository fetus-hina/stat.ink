<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\Spl3SalmonidAsset;
use app\components\i18n\Formatter;
use app\models\Salmon3;
use app\models\SalmonBossAppearance3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
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
                        'table',
                        'table-bordered',
                        'table-striped',
                    ],
                ]
            ),
            [
                'class' => 'table-responsive',
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
                            'scope' => 'col',
                            'class' => 'w-0 text-nowrap',
                        ],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon3', 'Defeated')),
                        [
                            'scope' => 'col',
                            'class' => 'w-0 text-nowrap',
                        ],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon3', 'Appearances')),
                        [
                            'scope' => 'col',
                            'class' => 'w-0 text-nowrap',
                        ],
                    ),
                    Html::tag(
                        'th',
                        '',
                        [],
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

        return Html::tag(
            'tbody',
            \implode('', ArrayHelper::getColumn($data, function (SalmonBossAppearance3 $model): string {
                return Html::tag(
                    'tr',
                    \implode('', [
                        Html::tag(
                            'td',
                            \vsprintf('%s %s', [
                                Html::img(
                                    Yii::$app->assetManager->getAssetUrl(
                                        Spl3SalmonidAsset::register($this->view),
                                        sprintf('%s.png', $model->boss->key),
                                    ),
                                    ['class' => 'basic-icon'],
                                ),
                                Html::encode(
                                    Yii::t('app-salmon-boss3', $model->boss->name),
                                ),
                            ]),
                            ['class' => 'w-0 text-nowrap'],
                        ),
                        Html::tag(
                            'td',
                            $model->defeated_by_me > 0
                                ? \vsprintf('%s <small>(%s)</small>', [
                                    Html::encode($this->formatter->asInteger($model->defeated)),
                                    Html::encode($this->formatter->asInteger($model->defeated_by_me)),
                                ])
                                : Html::encode($this->formatter->asInteger($model->defeated)),
                            ['class' => 'text-right'],
                        ),
                        Html::tag(
                            'td',
                            Html::encode($this->formatter->asInteger($model->appearances)),
                            ['class' => 'text-right'],
                        ),
                        Html::tag(
                            'td',
                            Html::encode('TODO'),
                            ['class' => 'text-muted'],
                        ),
                    ]),
                );
            })),
        );
    }
}
