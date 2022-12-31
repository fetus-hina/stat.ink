<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\XPowerHistoryAsset;
use app\models\Battle2;
use app\models\Lobby2;
use app\models\Mode2;
use app\models\Rank2;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class XPowerHistory extends Widget
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

        XPowerHistoryAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).xPowerHistory(%s,%s,%s,%s,%s);', [
            Json::encode('#' . $this->id),
            Json::encode('#' . $this->id . '-legends'),
            Json::encode([
                'estimate' => Yii::t('app', 'Estimated X Power'),
                'lose' => Yii::t('app', 'Lose'),
                'win' => Yii::t('app', 'Win'),
                'xPower' => Yii::t('app', 'X Power'),
            ]),
            Json::encode(array_map(
                function (Battle2 $model): ?float {
                    return $model->x_power_after < 1 ? null : (float)$model->x_power_after;
                },
                $history,
            )),
            Json::encode(array_map(
                function (Battle2 $model): ?float {
                    return $model->estimate_x_power < 1 ? null : (float)$model->estimate_x_power;
                },
                $history,
            )),
            Json::encode(array_map(
                function (Battle2 $model): ?bool {
                    return $model->is_win;
                },
                $history,
            )),
        ]));

        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag('div', '', [
                        'id' => $this->id,
                        'class' => [
                            'xpower-history',
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
                'xpower-history-container',
            ]],
        );
    }

    public function getHistory(): ?array
    {
        // {{{
        if (!$rankX = Rank2::findOne(['key' => 'x'])) {
            return null;
        }
        if (!$standardLobby = Lobby2::findOne(['key' => 'standard'])) {
            return null;
        }
        if (!$modeGachi = Mode2::findOne(['key' => 'gachi'])) {
            return null;
        }

        if (
            !$this->current ||
            $this->current->rule_id === null ||
            (!$this->current->x_power_after &&
            !$this->current->estimate_x_power) ||
            $this->current->rank_id !== $rankX->id ||
            // not Rank X
            $this->current->lobby_id !== $standardLobby->id ||
            // not Solo Queue
            $this->current->mode_id !== $modeGachi->id // not Ranked
        ) {
            return null;
        }

        $history = Battle2::find()
            ->andWhere(['and',
                [
                    '{{battle2}}.[[user_id]]' => $this->user->id,
                    '{{battle2}}.[[lobby_id]]' => $standardLobby->id,
                    '{{battle2}}.[[mode_id]]' => $modeGachi->id,
                    '{{battle2}}.[[rule_id]]' => $this->current->rule_id, // same rule
                ],
                ['<=', '{{battle2}}.[[id]]', $this->current->id],
            ])
            ->orderBy([
                '{{battle2}}.[[id]]' => SORT_DESC,
            ])
            ->limit(50)
            ->all();
        $isLost = false;
        $history = array_filter(
            $history,
            function (Battle2 $battle) use (&$isLost, $rankX): bool {
                if ($isLost) {
                    return false;
                }

                if (
                    $battle->rank_id !== $rankX->id ||
                    ($battle->x_power_after < 0 &&
                    $battle->estimate_x_power < 0)
                ) {
                    $isLost = true;
                    return false;
                }

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
