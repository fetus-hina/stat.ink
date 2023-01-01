<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use app\components\helpers\Weapon3MatchingCategory;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function array_filter;
use function array_map;
use function count;
use function implode;
use function strcmp;
use function strtolower;
use function usort;
use function vsprintf;

final class XMatchingCategory extends Widget
{
    public ?Battle3 $model = null;

    public function run(): string
    {
        $model = $this->model;
        if (
            $model?->lobby?->key !== 'xmatch' ||
            count($model?->battlePlayer3s) !== 8
        ) {
            return '';
        }

        $view = $this->view;
        if ($view instanceof View) {
            $view->registerCss($this->makeCss());
        }

        return implode('', [
            $this->renderHeading(),
            $this->renderSource(),
            $this->renderTable($model),
        ]);
    }

    private function renderHeading(): string
    {
        return Html::tag(
            'h2',
            'Matchmaking Group (Verifying)',
        );
    }

    private function renderSource(): string
    {
        return Html::tag(
            'p',
            Html::a(
                Html::encode('@sabot33n'),
                'https://twitter.com/sabot33n/status/1599075575272210433',
                [
                    'rel' => 'noopener noreferrer',
                    'target' => '_blank',
                ],
            ),
            ['class' => 'text-right'],
        );
    }

    private function renderTable(Battle3 $battle): string
    {
        return Html::tag(
            'table',
            Html::tag(
                'tbody',
                implode('', [
                    $this->renderOurTeam($battle),
                    $this->renderTheirTeam($battle),
                ]),
            ),
            [
                'class' => 'table table-bordered w-auto',
            ],
        );
    }

    private function renderOurTeam(Battle3 $battle): string
    {
        $data = $this->makeData($battle, ourTeam: true);
        return implode('', [
            $this->renderTeamCategoryRow($data),
            $this->renderTeamWeaponRow($data),
        ]);
    }

    private function renderTheirTeam(Battle3 $battle): string
    {
        $data = $this->makeData($battle, ourTeam: false);
        return implode('', [
            $this->renderTeamWeaponRow($data),
            $this->renderTeamCategoryRow($data),
        ]);
    }

    private function renderTeamCategoryRow(array $weapons): string
    {
        return Html::tag(
            'tr',
            implode('', array_map(
                fn (array $weapon): string => Html::tag(
                    'td',
                    $weapon['category'] ?? '?',
                    [
                        'class' => [
                            'fw-bold',
                            'text-center',
                            'weapon-matching-category-' . strtolower(($weapon['category'] ?? 'unknown')),
                        ],
                    ],
                ),
                $weapons,
            )),
        );
    }

    private function renderTeamWeaponRow(array $weapons): string
    {
        return Html::tag(
            'tr',
            implode('', array_map(
                fn (array $weapon): string => Html::tag(
                    'td',
                    WeaponIcon::widget(['model' => $weapon['weapon']]),
                    [
                        'class' => 'text-center',
                        'style' => [
                            'font-size' => '1.5em',
                        ],
                    ],
                ),
                $weapons,
            )),
        );
    }

    private function makeData(Battle3 $battle, bool $ourTeam): array
    {
        $players = array_filter(
            $battle->battlePlayer3s,
            fn (BattlePlayer3 $player): bool => $player->is_our_team === $ourTeam,
        );

        $results = [];
        foreach ($players as $player) {
            $weapon = $player->weapon;
            if ($weapon) {
                $results[] = [
                    'weapon' => $weapon,
                    'category' => Weapon3MatchingCategory::getCategory($weapon),
                ];
            } else {
                $results[] = [
                    'weapon' => null,
                    'category' => null,
                ];
            }
        }

        usort($results, function ($a, $b): int {
            if ($a['weapon'] === null) {
                return 1;
            }

            if ($b['weapon'] === null) {
                return -1;
            }

            if ($a['category'] === null) {
                return 1;
            }

            if ($b['category'] === null) {
                return -1;
            }

            return strcmp($a['category'], $b['category'])
                ?: strcmp($a['weapon']->key, $b['weapon']->key);
        });

        return $results;
    }

    private function makeCss(): string
    {
        $data = [
            [['a', 'b'], '#ffbf7f'],
            [['c', 'd', 'e'], '#7fbfff'],
            [['f', 'g'], '#bf7fbf'],
            [['h', 'i'], '#bfff7f'],
        ];

        return implode('', array_map(
            function (array $item): string {
                [$classes, $color] = $item;
                return vsprintf('%s{%s}', [
                    implode(',', array_map(
                        fn (string $c): string => ".weapon-matching-category-{$c}",
                        $classes,
                    )),
                    Html::cssStyleFromArray([
                        'background-color' => $color,
                    ]),
                ]);
            },
            $data,
        ));
    }
}
