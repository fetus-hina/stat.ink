<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\AppAsset;
use app\assets\CurrentTimeAsset;
use app\assets\ScheduleAsset;
use app\models\SalmonSchedule2;
use app\models\Schedule2;
use stdClass;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class IndexSchedule extends Widget
{
    public $battles;
    public $salmon;

    public function init()
    {
        parent::init();
        $this->battles = Schedule2::getInfo();
        $this->salmon = SalmonSchedule2::find()
            ->with(['map', 'weapons.weapon'])
            ->nowOrFuture()
            ->limit(2)
            ->all();
    }

    public function run()
    {
        // {{{
        if (!$this->hasCurrentSchedule) {
            return '';
        }

        $manip = function (?stdClass $schedule, string $key): ?stdClass {
            // {{{
            if (!$schedule || !($schedule->$key ?? null)) {
                return null;
            }

            return (object)[
                'term' => $schedule->_t,
                'data' => $schedule->$key,
            ];
            // }}}
        };

        $tabs = array_filter(
            [
                [
                    'key' => 'regular',
                    'icon' => GameModeIcon::spl2('nawabari'),
                    'label' => Yii::t('app-rule2', 'Regular'),
                    'enabled' => !!$this->getCurrentRegular(),
                    'contentClass' => indexSchedule\Battles::class,
                    'data' => [
                        $manip($this->battles->current ?? null, 'regular'),
                        $manip($this->battles->next ?? null, 'regular'),
                    ],
                ],
                [
                    'key' => 'gachi',
                    'icon' => GameModeIcon::spl2('gachi'),
                    'label' => Yii::t('app-rule2', 'Ranked'),
                    'enabled' => !!$this->getCurrentRanked(),
                    'contentClass' => indexSchedule\Battles::class,
                    'data' => [
                        $manip($this->battles->current ?? null, 'gachi'),
                        $manip($this->battles->next ?? null, 'gachi'),
                    ],
                ],
                [
                    'key' => 'league',
                    'icon' => GameModeIcon::spl2('league'),
                    'label' => Yii::t('app-rule2', 'League'),
                    'enabled' => !!$this->getCurrentLeague(),
                    'contentClass' => indexSchedule\Battles::class,
                    'data' => [
                        $manip($this->battles->current ?? null, 'league'),
                        $manip($this->battles->next ?? null, 'league'),
                    ],
                ],
                [
                    'key' => 'salmon',
                    'icon' => GameModeIcon::spl2('salmon'),
                    'labelFormat' => 'raw',
                    'label' => vsprintf('%s%s', [
                        $this->salmon && $this->salmonOpened
                            ? Html::tag(
                                'span',
                                (string)FA::fas('certificate')->fw(),
                                [
                                    'class' => 'text-warning auto-tooltip',
                                    'title' => Yii::t('app-salmon2', 'Open!'),
                                ]
                            )
                            : '',
                        Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
                    ]),
                    'enabled' => !!$this->salmon,
                    'contentClass' => indexSchedule\SalmonShifts::class,
                    'data' => $this->salmon,
                ],
            ],
            function (array $info): bool {
                return $info['enabled'];
            }
        );

        AppAsset::register($this->view);
        ScheduleAsset::register($this->view);
        return Html::tag(
            'aside',
            implode('', [
                $this->renderHeading(),
                $this->renderTabBar($tabs),
                $this->renderTabContents($tabs),
                $this->renderSource(),
            ]),
            [
                'id' => $this->id,
                'class' => [
                    'mb-3',
                    'index-schedule',
                ],
            ]
        );
        // }}}
    }

    public function renderHeading(): string
    {
        // {{{
        return Html::tag('h2', implode('', [
          Html::encode(Yii::t('app', 'Schedule')),
          $this->renderHeadingCurrentTime(),
        ]));
        // }}}
    }

    public function renderHeadingCurrentTime(): string
    {
        // {{{
        $id = sprintf('%s-current-time', $this->id);

        CurrentTimeAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).currentTime(%s, %s);', [
            Json::encode('#' . $id),
            Json::encode(Yii::$app->language), // not locale
            Json::encode(Yii::$app->timeZone)
        ]));
        return Html::tag(
            'span',
            vsprintf('[%s %s]', [
                Html::encode(Yii::t('app', 'Current Time:')),
                Html::tag(
                    'time',
                    Html::encode(implode(' ', [
                        Yii::$app->formatter->asDatetime($_SERVER['REQUEST_TIME'], 'short'),
                        Yii::$app->formatter->asDatetime($_SERVER['REQUEST_TIME'], 'z'),
                    ])),
                    ['id' => $id]
                ),
            ]),
            ['class' => 'small ml-2']
        );
        // }}}
    }

    public function renderTabBar(array $tabs): string
    {
        // {{{
        return Html::tag(
            'nav',
            Html::tag(
                'ul',
                implode('', array_map(
                    function (array $info, int $index): string {
                        return Html::tag(
                            'li',
                            Html::a(
                                trim(implode(' ', [
                                    $info['icon'] ?? '',
                                    Yii::$app->formatter->format(
                                        $info['label'],
                                        $info['labelFormat'] ?? 'text'
                                    ),
                                ])),
                                sprintf('#%s-tab-%s', $this->id, $info['key']),
                                [
                                    'data-toggle' => 'tab',
                                ]
                            ),
                            [
                                'class' => array_filter([
                                    $index === 0 ? 'active' : null,
                                ]),
                                'role' => 'tab',
                            ]
                        );
                    },
                    $tabs,
                    range(0, count($tabs) - 1),
                )),
                [
                    'id' => $this->id . '-tabs',
                    'class' => ['nav', 'nav-tabs'],
                    'role' => 'tablist',
                ]
            )
        );
        // }}}
    }

    public function renderTabContents(array $tabs): string
    {
        // {{{
        return Html::tag(
            'div',
            implode('', array_map(
                function (array $tab, int $index): string {
                    return Html::tag(
                        'div',
                        call_user_func(
                            [$tab['contentClass'], 'widget'],
                            ['data' => $tab]
                        ),
                        [
                            'id' => sprintf('%s-tab-%s', $this->id, $tab['key']),
                            'class' => array_filter([
                                'tab-pane',
                                $index === 0 ? 'active' : null,
                            ]),
                            'role' => 'tabpanel',
                        ]
                    );
                },
                $tabs,
                range(0, count($tabs) - 1)
            )),
            ['class' => 'tab-content']
        );
        // }}}
    }

    public function renderSource(): string
    {
        // {{{
        return Html::tag(
            'p',
            Yii::t('app', 'Source: {source}', [
                'source' => Html::a(
                    Html::encode('Splatoon2.ink'),
                    'https://splatoon2.ink/',
                    [
                        'rel' => 'external',
                        'target' => '_blank',
                    ]
                ),
            ]),
            ['class' => [
                'text-right',
                'mb-0',
            ]]
        );
        // }}}
    }

    public function getHasCurrentSchedule(): bool
    {
        if (!$current = $this->getCurrentSchedule()) {
            return false;
        }

        return ($current->regular ?? false) ||
            ($current->gachi ?? false) ||
            ($current->league ?? false);
    }

    public function getCurrentSchedule(): ?stdClass
    {
        return $this->battles->current ?? null;
    }

    public function getCurrentRegular(): ?stdClass
    {
        return $this->getCurrent('regular');
    }

    public function getCurrentRanked(): ?stdClass
    {
        return $this->getCurrent('gachi');
    }

    public function getCurrentLeague(): ?stdClass
    {
        return $this->getCurrent('league');
    }

    private function getCurrent($key): ?stdClass
    {
        if (!$current = $this->getCurrentSchedule()) {
            return null;
        }

        return $current->$key ?? null;
    }

    public function getSalmonOpened(): ?bool
    {
        if (!$current = ($this->salmon[0] ?? null)) {
            return null;
        }

        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $s = strtotime($current->start_at);

        return $s <= $t;
    }
}
