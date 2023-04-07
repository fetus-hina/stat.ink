<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\battle\panelItem;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\SalmonEggAsset;
use app\assets\SimpleBattleListAsset;
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\User;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function implode;
use function vsprintf;

final class SalmonItem3Widget extends Widget
{
    public ?User $user = null;
    public Salmon3 $model;
    public ?SalmonPlayer3 $myData = null;

    public function init()
    {
        parent::init();

        SimpleBattleListAsset::register($this->view);

        if (!$this->user) {
            $this->user = $this->model->user;
        }

        $tmp = ArrayHelper::getValue($this->model, 'salmonPlayer3s.0');
        if ($tmp instanceof SalmonPlayer3) {
            $this->myData = $tmp;
        }
    }

    public function run()
    {
        return Html::tag(
            'tr',
            Html::tag(
                'td',
                Html::a(
                    Html::tag(
                        'div',
                        implode('', [
                            Html::tag(
                                'div',
                                implode('', [
                                    $this->renderResultHtml(),
                                    $this->renderDataHtml(),
                                ]),
                                ['class' => 'simple-battle-row-impl-main'],
                            ),
                            Html::tag(
                                'div',
                                $this->renderDatetimeHtml(),
                                ['class' => 'simple-battle-at'],
                            ),
                        ]),
                        ['class' => 'simple-battle-row-impl'],
                    ),
                    ['salmon-v3/view',
                        'screen_name' => $this->user->screen_name,
                        'battle' => $this->model->uuid,
                    ],
                    [
                        'data' => [
                            'pjax' => '0',
                        ],
                    ],
                ),
                ['class' => 'simple-battle-row'],
            ),
        );
    }

    protected function renderResultHtml(): string
    {
        if ($this->model->clear_waves === null) {
            return Html::tag(
                'div',
                '?',
                ['class' => 'simple-battle-result simple-battle-result-unk'],
            );
        }

        $expectWaves = $this->model->is_eggstra_work ? 5 : 3;
        if ($this->model->clear_waves >= $expectWaves) {
            if ($this->model->is_eggstra_work || !$this->model->kingSalmonid) {
                return Html::tag(
                    'div',
                    Html::encode(Yii::t('app-salmon2', '✓')),
                    ['class' => 'simple-battle-result simple-battle-result-won'],
                );
            }

            return Html::tag(
                'div',
                implode('', [
                    Html::tag(
                        'div',
                        Html::encode(Yii::t('app-salmon2', '✓')),
                    ),
                    Html::tag(
                        'div',
                        Html::encode(Yii::t('app-salmon-boss3', $this->model->kingSalmonid->name)),
                        [
                            'class' => implode(' ', [
                                'small',
                                $this->model->clear_extra === null
                                    ? 'simple-battle-result-unk'
                                    : ($this->model->clear_extra
                                        ? 'simple-battle-result-won'
                                        : 'simple-battle-result-lost'
                                    ),
                            ]),
                        ],
                    ),
                ]),
                ['class' => 'simple-battle-result simple-battle-result-won'],
            );
        }

        return Html::tag(
            'div',
            Html::encode(Yii::t('app-salmon2', '✗')),
            ['class' => 'simple-battle-result simple-battle-result-lost'],
        );
    }

    protected function renderDataHtml(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::encode($this->getMapName()),
                    ['class' => 'simple-battle-map omit'],
                ),
                Html::tag(
                    'div',
                    Html::encode($this->getHazardLevel()),
                    ['class' => 'simple-battle-rule omit'],
                ),
                Html::tag(
                    'div',
                    Html::encode($this->getTitle()),
                    ['class' => 'simple-battle-weapon omit'],
                ),
                Html::tag(
                    'div',
                    $this->getEggs(),
                    ['class' => 'simple-battle-kill-death omit'],
                ),
            ]),
            ['class' => 'simple-battle-data'],
        );
    }

    protected function getMapName(): string
    {
        if (!$model = $this->model) {
            return '?';
        }

        if ($model->stage) {
            return Yii::t('app-map3', $model->stage->name);
        }

        if ($model->bigStage) {
            return Yii::t('app-map3', $model->bigStage->name);
        }

        return '?';
    }

    protected function getHazardLevel(): string
    {
        if ($this->model->danger_rate === null) {
            return vsprintf('%s: ?', [
                Yii::t('app-salmon2', 'Hazard Level'),
            ]);
        }

        return vsprintf('%s: %s', [
            Yii::t('app-salmon2', 'Hazard Level'),
            Yii::$app->formatter->asPercent((int)$this->model->danger_rate / 100, 0),
        ]);
    }

    protected function getTitle(): string
    {
        if (!$this->model->titleAfter) {
            return '?';
        }

        return vsprintf('%s %s', [
            Yii::t('app-salmon-title3', $this->model->titleAfter->name),
            $this->model->title_exp_after !== null
                ? Yii::$app->formatter->asInteger($this->model->title_exp_after)
                : '?',
        ]);
    }

    protected function getEggs(): string
    {
        $golden = '--';
        $power = '--';
        if ($this->myData) {
            if ($this->myData->golden_eggs !== null) {
                $golden = Yii::$app->formatter->asInteger((int)$this->myData->golden_eggs);
            }

            if ($this->myData->power_eggs !== null) {
                $power = Yii::$app->formatter->asInteger((int)$this->myData->power_eggs);
            }
        }

        $am = Yii::$app->assetManager;
        $asset = SalmonEggAsset::register($this->view);
        return vsprintf('%s %s %s %s', [
            Html::img(
                $am->getAssetUrl($asset, 'golden-egg.png'),
                [
                    'class' => 'auto-tooltip basic-icon',
                    'title' => Yii::t('app-salmon2', 'Golden Eggs'),
                ],
            ),
            $golden,
            Html::img(
                $am->getAssetUrl($asset, 'power-egg.png'),
                [
                    'class' => 'auto-tooltip basic-icon',
                    'title' => Yii::t('app-salmon2', 'Power Eggs'),
                ],
            ),
            $power,
        ]);
    }

    protected function renderDatetimeHtml(): string
    {
        if (!$this->model->start_at) {
            return '';
        }

        $dateTime = (new DateTimeImmutable($this->model->start_at))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));

        return Html::tag(
            'time',
            Html::encode(Yii::$app->formatter->asRelativeTime($dateTime)),
            [
              'datetime' => $dateTime->format(DateTime::ATOM),
              'title' => Yii::$app->formatter->asDatetime($dateTime, 'medium'),
              'class' => 'auto-tooltip',
            ],
        );
    }
}
