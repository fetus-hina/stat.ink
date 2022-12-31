<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\FestPowerHistoryAsset;
use app\models\Battle2;
use app\models\Mode2;
use app\models\Rule2;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class FestPowerHistory extends Widget
{
    public $user;
    public $current;

    public function init()
    {
        parent::init();

        if (!$this->user) {
            $this->user = $this->current->user;
        }
    }

    public function run(): string
    {
        if (!$history = $this->getHistory()) {
            return '';
        }

        FestPowerHistoryAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).festPowerHistory(%s);', [
            Json::encode('#' . $this->id),
            implode(', ', array_map([Json::class, 'encode'], [
                sprintf('#%s-legends', $this->id),
                [
                   'estimateBad' => Yii::t('app', 'Their team\'s splatfest power'),
                   'estimateGood' => Yii::t('app', 'My team\'s splatfest power'),
                   'festPower' => Yii::t('app', 'Splatfest Power'),
                   'lose' => Yii::t('app', 'Lose'),
                   'win' => Yii::t('app', 'Win'),
                ],
                array_map(
                    fn (Battle2 $model): ?float => $model->fest_power < 1 ? null : (float)$model->fest_power,
                    $history,
                ),
                array_map(
                    fn (Battle2 $model): ?float => $model->my_team_estimate_fest_power < 1
                            ? null
                            : (float)$model->my_team_estimate_fest_power,
                    $history,
                ),
                array_map(
                    fn (Battle2 $model): ?float => $model->his_team_estimate_fest_power < 1
                            ? null
                            : (float)$model->his_team_estimate_fest_power,
                    $history,
                ),
                array_map(
                    fn (Battle2 $model): ?bool => $model->is_win,
                    $history,
                ),
            ])),
        ]));

        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag('div', '', [
                        'id' => $this->id,
                        'class' => [
                            'fest-power-history',
                            'mb-1',
                        ],
                    ]),
                    ['class' => 'table-responsive'],
                ),
                Html::tag('div', '', [
                    'id' => $this->id . '-legends',
                ]),
            ]),
            ['class' => [
                'fest-power-history-container',
            ]],
        );
    }

    public function getHistory(): ?array
    {
        // {{{
        if (!$ruleTW = Rule2::findOne(['key' => 'nawabari'])) {
            return null;
        }

        if (!$modeFest = Mode2::findOne(['key' => 'fest'])) {
            return null;
        }
        if (
            !$this->current ||
            $this->current->rule_id !== $ruleTW->id ||
            $this->current->mode_id !== $modeFest->id ||
            $this->current->lobby_id === null
        ) {
            return null;
        }

        if (
            $this->current->fest_power < 1 &&
            $this->current->my_team_estimate_fest_power < 1 &&
            $this->current->his_team_estimate_fest_power < 1
        ) {
            return null;
        }
        $festPowerFilter = fn (string $column): array => ['and',
                ['not', ["{{battle2}}.[[{$column}]]" => null]],
                ['>', "{{battle2}}.[[{$column}]]", 0],
            ];
        $history = Battle2::find()
            ->andWhere(['and',
                [
                    '{{battle2}}.[[user_id]]' => $this->user->id,
                    '{{battle2}}.[[rule_id]]' => $ruleTW->id,
                    '{{battle2}}.[[mode_id]]' => $modeFest->id,
                    '{{battle2}}.[[lobby_id]]' => $this->current->lobby_id,
                ],
                ['<=', '{{battle2}}.[[id]]', $this->current->id],
                ['or',
                    $festPowerFilter('fest_power'),
                    $festPowerFilter('my_team_estimate_fest_power'),
                    $festPowerFilter('his_team_estimate_fest_power'),
                ],
            ])
            ->orderBy([
                '{{battle2}}.[[id]]' => SORT_DESC,
            ])
            ->limit(50)
            ->all();
        $isLost = false;
        $lastBattleTime = null;
        $history = array_filter(
            $history,
            function (Battle2 $battle) use (&$isLost, &$lastBattleTime): bool {
                if ($isLost) {
                    return false;
                }

                // 5.5 日以上間あいたなら、別のフェスとみなす
                $time = $battle->getVirtualStartTime();
                if (
                    $lastBattleTime !== null &&
                    $lastBattleTime->getTimestamp() - $time->getTimestamp() >= 5.5 * 86400
                ) {
                    $isLost = true;
                    return false;
                }
                $lastBattleTime = $time;

                return true;
            },
        );

        if (count($history) < 2) {
            return null;
        }

        // old -> new
        return array_reverse($history);
        // }}}
    }
}
