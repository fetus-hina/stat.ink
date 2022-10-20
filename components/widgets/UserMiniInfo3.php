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
use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Rank3;
use app\models\User;
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

        $groups = $this->getData($this->user);

        $stats = null;
        return Html::tag(
            'div',
            Html::tag(
                'div',
                \implode('', [
                    $this->renderHeading(),
                    \implode('<hr>', \array_filter(
                        [
                            $this->renderStatsTotal($groups),
                            $this->renderStatsLobbies($groups),
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
     * @param array{group: LobbyGroup3, stats: UserStat3[]}[] $models
     */
    private function renderStatsTotal(array $models): string
    {
        return Total::widget([
            'user' => $this->user,
            'statsList' => $this->flattenStats($models),
        ]);
    }

    /**
     * @param array{group: ?LobbyGroup3, stats: UserStat3[]}[] $models
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

        $peakRankInfo = $this->makePeakRankInfo(
            $this->flattenStats($models),
        );

        $defaultTab = $this->decideDefaultLobbyTab($models);

        return Tabs::widget([
            'items' => \array_filter(
                \array_map(
                    fn (array $groupInfo): ?array => ($groupInfo['group'])
                        ? [
                            'active' => $defaultTab === $groupInfo['group']->key,
                            'encode' => false,
                            'label' => $am && $iconAsset
                                ? Html::img(
                                    $am->getAssetUrl(
                                        $iconAsset,
                                        \sprintf('spl3/%s.png', \rawurlencode($groupInfo['group']->key)),
                                    ),
                                    [
                                        'class' => 'auto-tooltip',
                                        'title' => Yii::t('app-lobby3', $groupInfo['group']->name),
                                        'style' => [
                                            'height' => '16px',
                                            'width' => 'auto',
                                        ],
                                    ],
                                )
                                : Html::encode($groupInfo['group']->name),
                            'content' => \implode('', \array_map(
                                fn (UserStat3 $stat): string => Html::tag(
                                    'div',
                                    PerLobby::widget([
                                        'user' => $this->user,
                                        'model' => $stat,
                                        'peakRank' => $peakRankInfo,
                                    ]),
                                    ['class' => 'mt-2'],
                                ),
                                $groupInfo['stats'],
                            )),
                        ]
                        : null,
                    $models,
                ),
                fn (?array $conf): bool => $conf !== null,
            ),
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
     * @return array{group: ?LobbyGroup3, stats: UserStat3[]}[]
     */
    private function getData(User $user): array
    {
        $statsList = UserStat3::find()
            ->joinWith(
                ['lobby', 'lobby.group'],
                true,
            )
            ->with(['peakRank', 'user'])
            ->andWhere(['user_id' => (int)$user->id])
            ->orderBy([
                '{{%lobby_group3}}.rank' => SORT_ASC,
                '{{%lobby3}}.rank' => SORT_ASC,
            ])
            ->all();

        $results = [];
        foreach ($statsList as $stats) {
            $group = $stats->lobby ? $stats->lobby->group : null;
            $groupKey = $group ? $group->key : '';
            if (!isset($results[$groupKey])) {
                $results[$groupKey] = [
                    'group' => $group,
                    'stats' => [],
                ];
            }
            $results[$groupKey]['stats'][] = $stats;
        }

        return \array_values($results);
    }

    /**
     * @param array{group: ?LobbyGroup3, stats: UserStat3[]}[] $models
     * @return UserStat3[]
     */
    private function flattenStats(array $models): array
    {
        $statsList = [];
        foreach ($models as $group) {
            foreach ($group['stats'] as $stats) {
                $statsList[] = $stats;
            }
        }

        return $statsList;
    }

    /**
     * @param array{group: ?LobbyGroup3, stats: UserStat3[]}[] $models
     */
    private function decideDefaultLobbyTab(array $models): ?string
    {
        $result = null;
        $importance = -1;
        foreach ($models as $model) {
            $group = $model['group'];
            if ($group && $group->importance > $importance) {
                $result = $group->key;
                $importance = $group->importance;
            }
        }

        return $result;
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
