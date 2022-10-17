<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo;

use Yii;
use app\models\User;
use app\models\UserStat3;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\DetailView;

final class Total extends Widget
{
    public ?User $user = null;

    /**
     * @var UserStat3[]
     */
    public array $statsList = [];

    public function run(): string
    {
        $user = $this->user;
        if (!$user) {
            return '';
        }

        $battles = 0;
        $aggBattles = 0;
        $aggWins = 0;
        $aggKills = 0;
        $aggDeaths = 0;
        foreach ($this->statsList as $model) {
            $battles += (int)$model->battles;
            $aggBattles += (int)$model->agg_battles;
            $aggWins += (int)$model->wins;
            $aggKills += (int)$model->kills;
            $aggDeaths += (int)$model->deaths;
        }

        $fmt = Yii::$app->formatter;
        return Html::tag(
            'div',
            DetailView::widget([
                'options' => [
                    'tag' => 'div',
                ],
                'model' => [],
                'template' => Html::tag(
                    'div',
                    implode('', [
                        Html::tag('div', '{label}', [
                            'class' => 'user-label auto-tooltip',
                            'title' => '{label}',
                        ]),
                        Html::tag('div', '{value}', [
                            'class' => 'user-number',
                        ]),
                    ]),
                    ['class' => 'col-4 col-xs-4']
                ),
                'attributes' => [
                    [
                        'label' => Yii::t('app', 'Battles'),
                        'format' => 'raw',
                        'value' => fn (): string => Html::a(
                            Html::encode($fmt->asInteger($battles)),
                            ['show-v3/user',
                                'screen_name' => $user->screen_name,
                            ]
                        ),
                    ],
                    [
                        'label' => Yii::t('app', 'Win %'),
                        'value' => fn (): string => $aggBattles > 0
                            ? $fmt->asPercent($aggWins / $aggBattles, 1)
                            : Yii::t('app', 'N/A'),
                    ],
                    [
                        'label' => Yii::t('app', 'Kill Ratio'),
                        'value' => function () use ($fmt, $aggBattles, $aggKills, $aggDeaths): string {
                            if ($aggBattles < 1) {
                                return Yii::t('app', 'N/A');
                            }

                            if ($aggDeaths === 0) {
                                return $aggKills === 0
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(99.99, 2);
                            }

                            return $fmt->asDecimal($aggKills / $aggDeaths, 2);
                        },
                    ],
                ],
            ]),
            ['class' => 'row']
        );
    }
}
