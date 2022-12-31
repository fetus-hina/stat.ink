<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\battle\panelItem;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\SimpleBattleListAsset;
use app\components\widgets\ActiveRelativeTimeWidget;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;

class SalmonItem2Widget extends Widget
{
    public $user;
    public $model;
    public $myData;

    public function init()
    {
        parent::init();

        SimpleBattleListAsset::register($this->view);

        if (!$this->user) {
            $this->user = $this->model->user;
        }

        $this->myData = $this->model->myData;
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
                                ['class' => 'simple-battle-row-impl-main']
                            ),
                            Html::tag(
                                'div',
                                $this->renderDatetimeHtml(),
                                ['class' => 'simple-battle-at']
                            ),
                        ]),
                        ['class' => 'simple-battle-row-impl']
                    ),
                    ['salmon/view',
                        'screen_name' => $this->user->screen_name,
                        'id' => $this->model->id,
                    ],
                    [
                        'data' => [
                            'pjax' => '0',
                        ],
                    ],
                ),
                ['class' => 'simple-battle-row']
            )
        );
    }

    protected function renderResultHtml(): string
    {
        if ($this->model->clear_waves === null) {
            return Html::tag(
                'div',
                '?',
                ['class' => 'simple-battle-result simple-battle-result-unk']
            );
        }

        if ($this->model->clear_waves >= 3) {
            return Html::tag(
                'div',
                Html::encode(Yii::t('app-salmon2', '✓')),
                ['class' => 'simple-battle-result simple-battle-result-won']
            );
        }

        //TODO: fail reason
        return Html::tag(
            'div',
            Html::encode(Yii::t('app-salmon2', '✗')),
            ['class' => 'simple-battle-result simple-battle-result-lost']
        );
    }

    protected function renderDataHtml(): string
    {
        return Html::tag('div', implode('', [
            Html::tag(
                'div',
                Html::encode($this->getMapName()),
                ['class' => 'simple-battle-map omit']
            ),
            Html::tag(
                'div',
                Html::encode($this->getSpecialWeaponName()),
                ['class' => 'simple-battle-weapon omit']
            ),
            Html::tag(
                'div',
                Html::encode($this->getHazardLevel()),
                ['class' => 'simple-battle-rule omit']
            ),
            Html::tag(
                'div',
                Html::encode($this->getEggs()),
                ['class' => 'simple-battle-kill-death omit']
            ),
        ]), ['class' => 'simple-battle-data']);
    }

    protected function getMapName(): string
    {
        if (!$this->model->stage) {
            return '?';
        }

        return Yii::t('app-salmon-map2', $this->model->stage->name);
    }

    protected function getSpecialWeaponName(): string
    {
        if (!$this->myData || !$this->myData->special) {
            return '?';
        }

        return Yii::t('app-special2', $this->myData->special->name);
    }

    protected function getHazardLevel(): string
    {
        $value = '?';
        if ($this->model->danger_rate) {
            $value = Yii::$app->formatter->asDecimal((float)$this->model->danger_rate, 1);
        }

        return sprintf('%s: %s %%', Yii::t('app-salmon2', 'Hazard Level'), $value);
    }

    protected function getEggs(): string
    {
        $golden = '?';
        $power = '?';
        if ($this->myData) {
            if ($this->myData->golden_egg_delivered !== null) {
                $golden = Yii::$app->formatter->asInteger((int)$this->myData->golden_egg_delivered);
            }

            if ($this->myData->power_egg_collected !== null) {
                $power = Yii::$app->formatter->asInteger((int)$this->myData->power_egg_collected);
            }
        }

        return sprintf(
            '%s: %s, %s: %s',
            Yii::t('app-salmon2', 'Golden Eggs'),
            $golden,
            Yii::t('app-salmon2', 'Power Eggs'),
            $power
        );
    }

    protected function renderDatetimeHtml(): string
    {
        if ($this->model->end_at) {
            $at = $this->model->end_at;
        } elseif ($this->model->start_at) {
            $at = $this->model->start_at;
        } else {
            return '';
        }
        $dateTime = (new DateTimeImmutable($at))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));

        return Html::tag(
            'time',
            Html::encode(Yii::$app->formatter->asRelativeTime($dateTime)),
            [
              'datetime' => $dateTime->format(DateTime::ATOM),
              'title' => Yii::$app->formatter->asDatetime($dateTime, 'medium'),
              'class' => 'auto-tooltip',
            ]
        );
    }
}
