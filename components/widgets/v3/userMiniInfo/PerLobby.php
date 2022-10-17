<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo;

use Yii;
use app\components\widgets\v3\Rank;
use app\models\Rank3;
use app\models\User;
use app\models\UserStat3;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\DetailView;

final class PerLobby extends Widget
{
    public ?User $user = null;
    public ?UserStat3 $model = null;

    /**
     * @var array{Rank3, ?int}|null
     */
    public ?array $peakRank = null;

    public function run(): string
    {
        $user = $this->user;
        $model = $this->model;
        if (!$user || !$model) {
            return '';
        }

        return \implode('', [
            $this->renderHeader($user, $model),
            $this->renderContent($user, $model),
        ]);
    }

    private function renderHeader(User $user, UserStat3 $model): string
    {
        return Html::tag(
            'p',
            Html::encode(Yii::t('app-lobby3', $model->lobby->name)),
            ['class' => 'label-user mb-1'],
        );
    }

    private function renderContent(User $user, UserStat3 $model): string
    {
        $fmt = Yii::$app->formatter;
        return Html::tag(
            'div',
            DetailView::widget([
                'options' => [
                    'tag' => 'div',
                ],
                'model' => $model,
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
                'attributes' => $this->getListAttributes($user, $model),
            ]),
            ['class' => 'row']
        );
    }

    private function getListAttributes(User $user, UserStat3 $model): array
    {
        switch ($model->lobby->key) {
            case 'regular':
            case 'splatfest_challenge':
            case 'splatfest_open':
                return $this->getListAttributesForTurfWar($user, $model);

            case 'bankara_challenge':
            case 'bankara_open':
            // case 'league':
            // case 'xmatch':
                return $this->getListAttributesForBankara($user, $model);
        }

        return $this->getListDefaultAttributes($user, $model);
    }

    private function getListDefaultAttributes(User $user, UserStat3 $model): array
    {
        return [
            require __DIR__ . '/items/battles.php',
            require __DIR__ . '/items/win-pct.php',
            require __DIR__ . '/items/inked-per-min.php',
            require __DIR__ . '/items/kill-per-min.php',
            require __DIR__ . '/items/death-per-min.php',
            require __DIR__ . '/items/kill-ratio.php',
        ];
    }

    private function getListAttributesForTurfWar(User $user, UserStat3 $model): array
    {
        return [
            require __DIR__ . '/items/battles.php',
            require __DIR__ . '/items/win-pct.php',
            $model->lobby->key === 'splatfest_challenge'
                ? require __DIR__ . '/items/fest-power.php'
                : require __DIR__ . '/items/inked.php',
            require __DIR__ . '/items/kill.php',
            require __DIR__ . '/items/death.php',
            require __DIR__ . '/items/kill-ratio.php',
        ];
    }

    private function getListAttributesForBankara(User $user, UserStat3 $model): array
    {
        return [
            require __DIR__ . '/items/battles.php',
            require __DIR__ . '/items/win-pct.php',
            $this->attrPeakRank(),
            require __DIR__ . '/items/kill-per-min.php',
            require __DIR__ . '/items/death-per-min.php',
            require __DIR__ . '/items/kill-ratio.php',
        ];
    }

    private function attrPeakRank(): array
    {
        if (!$this->peakRank) {
            return require __DIR__ . '/items/empty.php';
        }

        return [
            'label' => Yii::t('app', 'Peak'),
            'format' => 'raw',
            'value' => function (): string {
                [$rank, $sPlus] = $this->peakRank;
                return Rank::widget([
                    'model' => $rank,
                    'splus' => $sPlus,
                ]);
            },
        ];
    }
}
