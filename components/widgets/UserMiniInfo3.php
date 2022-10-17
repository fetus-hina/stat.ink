<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\GameModeIconsAsset;
use app\assets\UserMiniinfoAsset;
use app\components\widgets\v3\userMiniInfo\PerLobby;
use app\components\widgets\v3\userMiniInfo\Total;
use app\models\Rank3;
use app\models\UserStat3;
use yii\base\Widget;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

use const SORT_ASC;

final class UserMiniInfo3 extends Widget
{
    public $id = 'user-miniinfo';
    public $user;

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            UserMiniinfoAsset::register($view);
        }

        $statsList = UserStat3::find()
            ->joinWith(['lobby'], true)
            ->with(['peakRank'])
            ->andWhere(['user_id' => (int)$this->user->id])
            ->orderBy(['{{%lobby3}}.[[rank]]' => SORT_ASC])
            ->all();

        $stats = null;
        return Html::tag(
            'div',
            Html::tag(
                'div',
                \implode('', [
                    $this->renderHeading(),
                    \implode('<hr>', \array_filter(
                        [
                            $this->renderStatsTotal($statsList),
                            $this->renderStatsLobbies($statsList),
                            $this->renderActivity(),
                        ],
                        fn (?string $item): bool => $item !== null,
                    )),
                ]),
                ['id' => $this->id . '-box']
            ),
            [
                'id' => $this->id,
                'itemprop' => 'author',
                'itemscope' => true,
                'itemtype' => 'http://schema.org/Person',
            ]
        );
    }

    private function renderHeading(): string
    {
        return Html::tag(
            'h2',
            Html::a(
                implode('', [
                    $this->renderUserIcon(),
                    $this->renderUserName(),
                ]),
                ['show-user/profile',
                    'screen_name' => $this->user->screen_name,
                ]
            )
        );
    }

    private function renderUserIcon(): string
    {
        return Html::tag(
            'span',
            UserIcon::widget([
                'user' => $this->user,
                'options' => [
                    'height' => '48',
                    'width' => '48',
                ],
            ]),
            ['class' => 'miniinfo-user-icon']
        );
    }

    private function renderUserName(): string
    {
        return Html::tag(
            'span',
            Html::encode($this->user->name),
            [
                'class' => 'miniinfo-user-name',
                'itemprop' => 'name',
            ]
        );
    }

    /**
     * @param UserStat3[] $models
     */
    private function renderStatsTotal(array $models): string
    {
        return Total::widget([
            'user' => $this->user,
            'statsList' => $models,
        ]);
    }

    /**
     * @param UserStat3[] $models
     */
    private function renderStatsLobbies(array $models): ?string
    {
        if (!$models) {
            return null;
        }

        $view = $this->view;
        if ($view instanceof View) {
            $am = Yii::$app->assetManager;
            $iconAsset = GameModeIconsAsset::register($view);

            $view->registerCss(\vsprintf('#%s .nav>li>a{%s}', [
                $this->id,
                'padding:5px 10px',
            ]));
        }

        $peakRankInfo = $this->makePeakRankInfo($models);

        return Tabs::widget([
            'items' => \array_map(
                fn (UserStat3 $model): array => [
                    'encode' => false,
                    'label' => $am && $iconAsset
                        ? Html::img(
                            $am->getAssetUrl(
                                $iconAsset,
                                \sprintf('spl3/%s.png', \rawurlencode($model->lobby->key)),
                            ),
                            [
                                'class' => 'auto-tooltip',
                                'title' => Yii::t('app-lobby3', $model->lobby->name),
                                'style' => [
                                    'height' => '16px',
                                    'width' => 'auto',
                                ],
                            ],
                        )
                        : Html::encode($model->lobby->name),
                    'content' => PerLobby::widget([
                        'user' => $this->user,
                        'model' => $model,
                        'peakRank' => $peakRankInfo,
                    ]),
                ],
                $models,
            ),
            'tabContentOptions' => [
                'class' => 'mt-2',
            ],
        ]);
    }

    private function renderActivity(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app', 'Activity')),
                    ['class' => 'label-user']
                ),
                Html::tag(
                    'div',
                    ActivityWidget::widget([
                        'user' => $this->user,
                        'months' => 4,
                        'longLabel' => false,
                        'size' => 9,
                        'only' => 'spl3',
                    ]),
                    ['class' => 'table-responsive bg-white']
                ),
            ]),
            ['class' => 'miniinfo-databox']
        );
    }

    /**
     * @param UserStat3[] $models
     * @return array{0: Rank3, 1: ?int}|null
     */
    private function makePeakRankInfo(array $models): ?array
    {
        /**
         * @var array{0: Rank3, 1: ?int}
         */
        $list = \array_filter(
            \array_map(
                fn (UserStat3 $model): ?array => $model->peakRank
                    ? [$model->peakRank, $model->peak_s_plus]
                    : null,
                $models,
            ),
            fn (?array $info): bool => $info !== null,
        );

        if (!$list) {
            return null;
        }

        \usort(
            $list,
            fn (array $a, array $b): int => $b[0]->rank <=> $a[0]->rank
                ?: (int)($b[1] ?? -1) <=> (int)($a[1] ?? -1)
        );

        return $list[0];
    }
}
