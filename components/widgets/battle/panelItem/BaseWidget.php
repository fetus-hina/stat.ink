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
use Yii;
use app\assets\SimpleBattleListAsset;
use yii\base\Widget;
use yii\bootstrap\Html;

use function array_filter;
use function implode;
use function sprintf;

abstract class BaseWidget extends Widget
{
    public $model;

    abstract public function getBattleEndAt(): ?DateTimeImmutable;

    abstract public function getIsKO(): ?bool;

    abstract public function getIsWin(): ?bool;

    abstract public function getKillDeath(): array;

    abstract public function getLinkRoute(): array;

    abstract public function getMapName(): string;

    abstract public function getRuleName(): string;

    abstract public function getWeaponName(): string;

    public function getIsDraw(): ?bool
    {
        return null;
    }

    public function getWeaponIcon(): ?string
    {
        return null;
    }

    public function getSubSpIcon(): ?string
    {
        return null;
    }

    public function run()
    {
        SimpleBattleListAsset::register($this->view);
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
                    $this->getLinkRoute(),
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
        $isDraw = $this->getIsDraw();
        $result = $this->getIsWin();
        $isKO = $this->getIsKO();
        if ($result === null) {
            return Html::tag(
                'div',
                '?',
                ['class' => 'simple-battle-result simple-battle-result-unk'],
            );
        }

        if ($isDraw) {
            return Html::tag(
                'div',
                Html::encode(Yii::t('app', 'Draw')),
                ['class' => 'simple-battle-result simple-battle-result-unk'],
            );
        }

        if ($isKO === null) {
            $koHtml = '';
        } else {
            $koHtml = '<br>' . Html::encode(Yii::t('app', $isKO ? 'K.O.' : 'Time'));
        }
        return Html::tag(
            'div',
            Html::encode(Yii::t('app', $result ? 'Won' : 'Lost')) . $koHtml,
            ['class' => [
                'simple-battle-result',
                $result ? 'simple-battle-result-won' : 'simple-battle-result-lost',
            ]],
        );
    }

    protected function renderDataHtml(): string
    {
        return Html::tag('div', implode('', [
            Html::tag(
                'div',
                Html::encode($this->getMapName()),
                ['class' => 'simple-battle-map omit'],
            ),
            Html::tag(
                'div',
                Html::encode($this->getRuleName()),
                ['class' => 'simple-battle-rule omit'],
            ),
            Html::tag(
                'div',
                implode(' ', array_filter([
                    $this->getWeaponIcon(),
                    Html::encode($this->getWeaponName()),
                    $this->getSubSpIcon(),
                ])),
                ['class' => 'simple-battle-weapon omit'],
            ),
            Html::tag(
                'div',
                $this->renderKillDeathHtml(),
                ['class' => 'simple-battle-kill-death omit'],
            ),
        ]), ['class' => 'simple-battle-data']);
    }

    protected function renderKillDeathHtml(): string
    {
        [$kill, $death] = $this->getKillDeath();
        return implode('', [
            sprintf(
                '%sK / %sD',
                $kill === null ? '?' : (string)(int)$kill,
                $death === null ? '?' : (string)(int)$death,
            ),
            ' ',
            (function () use ($kill, $death): string {
                if ($kill === null || $death === null) {
                    return '';
                }
                if ($kill == $death) {
                    return Html::tag('span', '=', ['class' => 'label label-default']);
                } elseif ($kill > $death) {
                    return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
                } else {
                    return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
                }
            })(),
        ]);
    }

    protected function renderDatetimeHtml(): string
    {
        if (!$datetime = $this->getBattleEndAt()) {
            return '';
        }
        return Html::tag(
            'time',
            Html::encode(Yii::$app->formatter->asRelativeTime($datetime)),
            [
              'datetime' => $datetime->format(DateTime::ATOM),
              'title' => Yii::$app->formatter->asDatetime($datetime, 'medium'),
              'class' => 'auto-tooltip',
            ],
        );
    }
}
