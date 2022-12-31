<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\LeaguePowerHistoryAsset;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle2;
use app\models\Lobby2;
use app\models\Mode2;
use app\models\Rank2;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class LeaguePowerHistory extends Widget
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

        $max = array_reduce(
            array_filter(
                $history,
                function (Battle2 $item): bool {
                    return filter_var($item->league_point, FILTER_VALIDATE_FLOAT) !== false;
                },
            ),
            function (?array $carry, Battle2 $item): ?array {
                $oldValue = $carry[0] ?? 0.0;
                if ($oldValue < (float)$item->league_point) {
                    return [
                        (float)$item->league_point,
                        Url::to(['show-v2/battle',
                            'screen_name' => $this->user->screen_name,
                            'battle' => $item->id,
                        ]),
                    ];
                }

                return $carry;
            },
            null,
        );
        if ($max === null) {
            return '';
        }

        $period = BattleHelper::periodToRange2($this->current->period);
        $maxEver = $this->getSameTeamMax();

        LeaguePowerHistoryAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).leaguePowerHistory(%s);', [
            Json::encode('#' . $this->id),
            implode(',', [
                Json::encode('#' . $this->id . '-legends'),
                Json::encode([
                    'highestCurrent' => $max
                        ? Html::a(
                            Html::encode(vsprintf('%s: %s', [
                                Yii::t('app', 'Highest (current period)'),
                                Yii::$app->formatter->asDecimal($max[0], 1),
                            ])),
                            $max[1],
                        )
                        : Html::encode(Yii::t('app', 'Highest (current period)')),
                    'highestEver' => $maxEver
                        ? Html::a(
                            Html::encode(vsprintf('%s: %s', [
                                Yii::t('app', 'Highest (this teammates)'),
                                Yii::$app->formatter->asDecimal($maxEver[0], 1),
                            ])),
                            $maxEver[1],
                        )
                        : Html::encode(Yii::t('app', 'Highest (this teammates)')),
                    'leaguePower' => Yii::t('app', 'League Power'),
                    'lose' => Yii::t('app', 'Lose'),
                    'win' => Yii::t('app', 'Win'),
                ]),
                (string)($period[0] - 60) . '000', // unixtime in milliseconds
                (string)($period[1] + 590) . '000', // unixtime in milliseconds
                Json::encode($max), // max data in "current" period
                Json::encode($maxEver), // max data in "past" period
                Json::encode(array_map(
                    function (Battle2 $model): array {
                        $value = filter_var($model->league_point, FILTER_VALIDATE_FLOAT);
                        return [
                            'time' => (int)$model->getVirtualStartTime()->format('U') * 1000,
                            'value' => is_float($value) ? $value : null,
                            'isWin' => $model->is_win,
                            'url' => Url::to(['show-v2/battle',
                                'screen_name' => $this->user->screen_name,
                                'battle' => $model->id,
                            ]),
                        ];
                    },
                    $history,
                )),
            ]),
        ]));

        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag('div', '', [
                        'id' => $this->id,
                        'class' => [
                            'league-power-history',
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
        $model = $this->current;

        if (
            !$model->my_team_id ||
            !$model->lobby_id ||
            !$model->mode_id ||
            !$model->rule_id ||
            !$model->period ||
            !in_array($model->lobby->key ?? '', ['squad_2', 'squad_4'], true) ||
            ($model->mode->key ?? '') !== 'gachi' ||
            !in_array($model->rule->key ?? '', ['area', 'yagura', 'hoko', 'asari'], true)
        ) {
            return null;
        }

        $query = Battle2::find()
            ->andWhere([
                'user_id' => (int)$model->user_id,
                'lobby_id' => (int)$model->lobby_id,
                'mode_id' => (int)$model->mode_id,
                'rule_id' => (int)$model->rule_id,
                'period' => (int)$model->period,
                'my_team_id' => $model->my_team_id,
            ])
            ->andWhere(['not', ['is_win' => null]]);
        if ($model->splatnet_number) {
            $query->andWhere(['not', ['splatnet_number' => null]])
                ->orderBy(['splatnet_number' => SORT_ASC]);
        } else {
            $query->orderBy(['id' => SORT_ASC]);
        }

        $list = $query->all();
        return count($list) < 2 ? null : $list;
    }

    public function getSameTeamMax(): ?array
    {
        $model = $this->current;

        if (
            !$model->my_team_id ||
            !$model->lobby_id ||
            !$model->mode_id ||
            !$model->rule_id ||
            !in_array($model->lobby->key ?? '', ['squad_2', 'squad_4'], true) ||
            ($model->mode->key ?? '') !== 'gachi' ||
            !in_array($model->rule->key ?? '', ['area', 'yagura', 'hoko', 'asari'], true)
        ) {
            return null;
        }

        // 浮動小数点数に変換すると、規定により悲惨な目にあうので
        // 文字列型のまま処理すること
        $maxPower = Battle2::find()
            ->andWhere([
                'user_id' => (int)$model->user_id,
                'lobby_id' => (int)$model->lobby_id,
                'mode_id' => (int)$model->mode_id,
                'rule_id' => (int)$model->rule_id,
                'my_team_id' => $model->my_team_id,
            ])
            ->andWhere(['not', ['is_win' => null]])
            ->andWhere(['<=', 'period', (int)$model->period])
            ->max('league_point');
        if (filter_var($maxPower, FILTER_VALIDATE_FLOAT) === false) {
            return null;
        }

        $maxData = Battle2::find()
            ->andWhere([
                'user_id' => (int)$model->user_id,
                'lobby_id' => (int)$model->lobby_id,
                'mode_id' => (int)$model->mode_id,
                'rule_id' => (int)$model->rule_id,
                'my_team_id' => $model->my_team_id,
                'league_point' => $maxPower,
            ])
            ->andWhere(['not', ['is_win' => null]])
            ->andWhere(['<=', 'period', (int)$model->period])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
        if (!$maxData) {
            return null;
        }

        return [
            (float)$maxData->league_point,
            Url::to(['show-v2/battle',
                'screen_name' => $this->user->screen_name,
                'battle' => $maxData->id,
            ]),
        ];
    }
}
