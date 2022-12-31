<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Special2;
use app\models\SplatoonVersionGroup2;
use app\models\Subweapon2;
use app\models\User;
use app\models\UserWeapon2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Battle2FilterWidget extends Widget
{
    public $id = 'filter-form';
    public $route;
    public $screen_name;
    public $filter;

    public $rule = true;
    public $map = true;
    public $weapon = true;
    public $rank = true;
    public $result = true;
    public $connectivity = false;
    public $term = true;
    public $filterText = false;
    public $withTeam = false;
    public $action = 'search'; // search or summarize

    public function run()
    {
        ob_start();
        try {
            $divId = $this->getId();
            $this->view->registerCss(sprintf(
                '#%s{%s}',
                $divId,
                Html::cssStyleFromArray([
                    'border' => '1px solid #ccc',
                    'border-radius' => '5px',
                    'padding' => '15px',
                    'margin-bottom' => '15px',
                ]),
            ));
            echo Html::beginTag('div', ['id' => $divId]);
            $form = ActiveForm::begin([
                'id' => $this->id,
                'action' => [ $this->route, 'screen_name' => $this->screen_name ],
                'method' => 'get',
            ]);
            echo $this->drawFields($form);
            ActiveForm::end();
            echo Html::endTag('div');
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    protected function drawFields(ActiveForm $form): string
    {
        $ret = [];
        if ($this->rule) {
            $ret[] = $this->drawRule($form);
        }
        if ($this->map) {
            $ret[] = $this->drawMap($form);
        }
        if ($this->weapon) {
            $ret[] = $this->drawWeapon($form);
        }
        if ($this->rank) {
            $ret[] = $this->drawRank($form);
        }
        if ($this->result) {
            $ret[] = $this->drawResult($form);
        }
        if ($this->connectivity) {
            $ret[] = $this->drawConnectivity($form);
        }
        if ($this->term) {
            $ret[] = $this->drawTerm($form);
            $ret[] = Html::hiddenInput(
                sprintf('%s[%s]', $this->filter->formName(), 'timezone'),
                Yii::$app->timeZone,
            );
        }
        if ($this->filterText) {
            $ret[] = $this->drawFilter($form);
            if ($this->withTeam && $this->filter->filterWithPrincipalId) {
                $ret[] = $this->drawWithTeam($form);
            }
        }
        switch ($this->action) {
            case 'summarize':
                $ret[] = Html::tag(
                    'button',
                    Yii::t('app', 'Summarize'),
                    [
                        'type' => 'submit',
                        'class' => [ 'btn', 'btn-primary' ],
                    ],
                );
                break;

            case 'search':
            default:
                $ret[] = Html::tag(
                    'button',
                    implode(' ', [
                        Icon::search(),
                        Html::encode(Yii::t('app', 'Search')),
                    ]),
                    [
                        'type' => 'submit',
                        'class' => [ 'btn', 'btn-primary' ],
                    ],
                );
        }
        return implode('', $ret);
    }

    protected function drawRule(ActiveForm $form): string
    {
        $regular    = Yii::t('app-rule2', 'Regular');
        $gachi      = Yii::t('app-rule2', 'Ranked');
        $rankLeague = Yii::t('app-rule2', 'Ranked + League');
        $league     = Yii::t('app-rule2', 'League Battle');
        $league2    = Yii::t('app-rule2', 'League (Twin)');
        $league4    = Yii::t('app-rule2', 'League (Quad)');
        $private    = Yii::t('app-rule2', 'Private');

        $any        = Yii::t('app-rule2', 'Any Mode');
        $nawabari   = Yii::t('app-rule2', 'Turf War');
        $area       = Yii::t('app-rule2', 'Splat Zones');
        $yagura     = Yii::t('app-rule2', 'Tower Control');
        $hoko       = Yii::t('app-rule2', 'Rainmaker');
        $asari      = Yii::t('app-rule2', 'Clam Blitz');

        $list = [
            '' => Yii::t('app-rule2', 'Any Mode'),
            Yii::t('app-rule2', 'Regular Battle') => [
                'standard-regular-nawabari' => "{$nawabari} ({$regular})",
            ],
            Yii::t('app-rule2', 'Ranked Battle') => [
                'standard-gachi-any' => "{$any} ({$gachi})",
                'standard-gachi-area' => "{$area} ({$gachi})",
                'standard-gachi-yagura' => "{$yagura} ({$gachi})",
                'standard-gachi-hoko' => "{$hoko} ({$gachi})",
                'standard-gachi-asari' => "{$asari} ({$gachi})",
            ],
            $rankLeague => [
                'any-gachi-any' => "{$any} ({$rankLeague})",
                'any-gachi-area' => "{$area} ({$rankLeague})",
                'any-gachi-yagura' => "{$yagura} ({$rankLeague})",
                'any-gachi-hoko' => "{$hoko} ({$rankLeague})",
                'any-gachi-asari' => "{$asari} ({$rankLeague})",
            ],
            Yii::t('app-rule2', 'League Battle') => [
                'any_squad-gachi-any' => "{$any} ({$league})",
                'any_squad-gachi-area' => "{$area} ({$league})",
                'any_squad-gachi-yagura' => "{$yagura} ({$league})",
                'any_squad-gachi-hoko' => "{$hoko} ({$league})",
                'any_squad-gachi-asari' => "{$asari} ({$league})",
            ],
            Yii::t('app-rule2', 'League Battle (Twin)') => [
                'squad_2-gachi-any' => "{$any} ({$league2})",
                'squad_2-gachi-area' => "{$area} ({$league2})",
                'squad_2-gachi-yagura' => "{$yagura} ({$league2})",
                'squad_2-gachi-hoko' => "{$hoko} ({$league2})",
                'squad_2-gachi-asari' => "{$asari} ({$league2})",
            ],
            Yii::t('app-rule2', 'League Battle (Quad)') => [
                'squad_4-gachi-any' => "{$any} ({$league4})",
                'squad_4-gachi-area' => "{$area} ({$league4})",
                'squad_4-gachi-yagura' => "{$yagura} ({$league4})",
                'squad_4-gachi-hoko' => "{$hoko} ({$league4})",
                'squad_4-gachi-asari' => "{$asari} ({$league4})",
            ],
            Yii::t('app-rule2', 'Splatfest') => [
                'any-fest-nawabari' => Yii::t('app-rule2', 'Splatfest'),
                'fest_normal-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Normal)'),
                'standard-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Pro/Solo)'),
                'squad_4-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Team)'),
            ],
            Yii::t('app-rule2', 'Private Battle') => [
                'private-private-any' => "{$any} ({$private})",
                'private-private-nawabari' => "{$nawabari} ({$private})",
                'private-private-gachi' => "{$gachi} ({$private})",
                'private-private-area' => "{$area} ({$private})",
                'private-private-yagura' => "{$yagura} ({$private})",
                'private-private-hoko' => "{$hoko} ({$private})",
                'private-private-asari' => "{$asari} ({$private})",
            ],
        ];
        return (string)$form
            ->field($this->filter, 'rule')
            ->dropDownList($list)
            ->label(false);
    }

    protected function drawMap(ActiveForm $form): string
    {
        return (string)$form
            ->field($this->filter, 'map')
            ->dropDownList(array_merge(
                ['' => Yii::t('app-map2', 'Any Stage')],
                ArrayHelper::map(
                    Map2::sort(Map2::find()->all()),
                    'key',
                    fn (Map2 $map): string => Yii::t('app-map2', $map->name),
                ),
            ))
            ->label(false);
    }

    protected function drawWeapon(ActiveForm $form): string
    {
        $user = User::findOne(['screen_name' => $this->screen_name]);
        $weaponIdList = $this->getUsedWeaponIdList($user);
        $list = array_merge(
            $this->createMainWeaponList($weaponIdList),
            $this->createGroupedMainWeaponList($weaponIdList),
            $this->createSubWeaponList($weaponIdList),
            $this->createSpecialWeaponList($weaponIdList),
        );
        return (string)$form->field($this->filter, 'weapon')->dropDownList($list)->label(false);
    }

    /**
     * @return int[]|null
     */
    protected function getUsedWeaponIdList(User $user = null): ?array
    {
        if (!$user) {
            return null;
        }

        return ArrayHelper::getColumn(
            UserWeapon2::find()
                ->andWhere(['user_id' => $user->id])
                ->all(),
            fn (UserWeapon2 $model): int => (int)ArrayHelper::getValue($model, 'weapon_id'),
        );
    }

    protected function createMainWeaponList(array $weaponIdList): array
    {
        $ret = [];
        $q = WeaponCategory2::find()
            ->orderBy(['id' => SORT_ASC])
            ->with([
                'weaponTypes' => function (ActiveQuery $query): void {
                    $query->orderBy(['id' => SORT_ASC]);
                },
                'weaponTypes.weapons' => function (ActiveQuery $query) use ($weaponIdList): void {
                    $query->andWhere(['id' => $weaponIdList]);
                },
            ]);
        foreach ($q->all() as $category) {
            $categoryName = Yii::t('app-weapon2', $category->name);
            foreach ($category->weaponTypes as $type) {
                $typeName = Yii::t('app-weapon2', $type->name);
                $groupLabel = $categoryName !== $typeName
                    ? sprintf('%s » %s', $categoryName, $typeName)
                    : $typeName;
                $weapons = ArrayHelper::map(
                    $type->weapons, // already filtered (see "with" above)
                    'key',
                    fn (Weapon2 $weapon): string => Yii::t('app-weapon2', $weapon->name),
                );
                if ($weapons) {
                    uasort($weapons, 'strnatcasecmp');
                    $ret[$groupLabel] = count($weapons) > 1
                        ? array_merge(
                            ['@' . $type->key => Yii::t('app-weapon2', 'All of {0}', $typeName)],
                            $weapons,
                        )
                        : $weapons;
                }
            }
        }
        return array_merge(
            ['' => Yii::t('app-weapon2', 'Any Weapon')],
            $ret,
        );
    }

    protected function createGroupedMainWeaponList(array $weaponIdList): array
    {
        return [
            Yii::t('app', 'Main Weapon') => (function () use ($weaponIdList): array {
                $ret = [];
                $subQuery = (new \yii\db\Query())
                    ->select(['id' => '{{weapon2}}.[[main_group_id]]'])
                    ->from('weapon2')
                    ->andWhere(['in', '{{weapon2}}.[[id]]', $weaponIdList]);
                $list = Weapon2::find()
                    ->andWhere(['{{weapon2}}.[[id]]' => $subQuery])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['~' . $item['key']] = Yii::t('app', '{0} etc.', [
                        Yii::t('app-weapon2', $item['name']),
                    ]);
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })(),
        ];
    }

    protected function createSubWeaponList(array $weaponIdList): array
    {
        return [
            Yii::t('app', 'Sub Weapon') => (function () use ($weaponIdList): array {
                $ret = [];
                $list = SubWeapon2::find()
                    ->innerJoinWith('weapons')
                    ->andWhere(['{{weapon2}}.[[id]]' => $weaponIdList])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['+' . $item['key']] = Yii::t('app-subweapon2', $item['name']);
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })(),
        ];
    }

    protected function createSpecialWeaponList(array $weaponIdList)
    {
        return [
            Yii::t('app', 'Special') => (function () use ($weaponIdList) {
                $ret = [];
                $list = Special2::find()
                    ->innerJoinWith('weapons')
                    ->andWhere(['{{weapon2}}.[[id]]' => $weaponIdList])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['*' . $item['key']] = Yii::t('app-special2', $item['name']);
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })(),
        ];
    }

    protected function drawRank(ActiveForm $form): string
    {
        $groups = RankGroup2::find()
            ->with([
                'ranks' => fn ($q) => $q->orderBy('[[id]] DESC'),
            ])
            ->orderBy('[[id]] DESC')
            ->asArray()
            ->all();

        $list = [];
        $list[''] = Yii::t('app-rank', 'Any Rank');
        foreach ($groups as $group) {
            $list['~' . $group['key']] = Yii::t('app-rank2', $group['name']);
            foreach ($group['ranks'] as $i => $rank) {
                $list[$rank['key']] = sprintf(
                    '%s %s',
                    ($i !== count($group['ranks']) - 1 ? '├' : '└'),
                    Yii::t('app-rank2', $rank['name']),
                );
            }
        }
        return (string)$form->field($this->filter, 'rank')->dropDownList($list)->label(false);
    }

    protected function drawResult(ActiveForm $form): string
    {
        $list = [
            ''      => Yii::t('app', 'Won / Lost'),
            'win'   => Yii::t('app', 'Won'),
            'lose'  => Yii::t('app', 'Lost'),
        ];
        return (string)$form->field($this->filter, 'result')->dropDownList($list)->label(false);
    }

    protected function drawConnectivity(ActiveForm $form): string
    {
        $list = [
            ''      => Yii::t('app', 'Connectivity'),
            'yes'   => Yii::t('app', 'Has disconnected player'),
            'no'    => Yii::t('app', 'Hasn\'t disconnected player'),
        ];
        return (string)$form->field($this->filter, 'has_disconnect')
            ->dropDownList($list)
            ->label(false);
    }

    protected function drawFilter(ActiveForm $form): string
    {
        return (string)$form->field($this->filter, 'filter')
            ->textInput([
              'placeholder' => Yii::t('app', 'Filter Query'),
            ])
            ->label(false);
    }

    protected function drawWithTeam(ActiveForm $form): string
    {
        return (string)$form->field($this->filter, 'with_team')
            ->dropDownList(
                [
                    'good' => Yii::t('app', 'Good Guys'),
                    'bad' => Yii::t('app', 'Bad Guys'),
                ],
                ['prompt' => Yii::t('app', 'Target Player\'s Team')],
            )
            ->label(false);
    }

    protected function drawTerm(ActiveForm $form)
    {
        return $this->drawTermMain($form) . $this->drawTermPeriod($form);
    }

    protected function drawTermMain(ActiveForm $form): string
    {
        $list = [
            ''                  => Yii::t('app', 'Any Time'),
            'this-period'       => Yii::t('app', 'Current Period'),
            'last-period'       => Yii::t('app', 'Previous Period'),
            'last-2-periods'    => Yii::t('app', 'Last {n} Periods', ['n' => 2]),
            'last-3-periods'    => Yii::t('app', 'Last {n} Periods', ['n' => 3]),
            'last-4-periods'    => Yii::t('app', 'Last {n} Periods', ['n' => 4]),
            '24h'               => Yii::t('app', 'Last 24 Hours'),
            'today'             => Yii::t('app', 'Today'),
            'yesterday'         => Yii::t('app', 'Yesterday'),
            'this-month-utc'    => Yii::t('app', 'This Month (UTC)'),
            'last-month-utc'    => Yii::t('app', 'Last Month (UTC)'),
            'last-10-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  10]),
            'last-20-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  20]),
            'last-50-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  50]),
            'last-100-battles'  => Yii::t('app', 'Last {n} Battles', ['n' => 100]),
            'last-200-battles'  => Yii::t('app', 'Last {n} Battles', ['n' => 200]),
            'this-fest'         => Yii::t('app', 'Current/Last Splatfest'),
            'term'              => Yii::t('app', 'Specify Period'),
        ];

        $versions = (function (): array {
            $result = [];
            $groups = SplatoonVersionGroup2::find()->with('versions')->asArray()->all();
            usort($groups, fn (array $a, array $b): int => version_compare($b['tag'], $a['tag']));
            foreach ($groups as $group) {
                $n = count($group['versions']);
                if ($n == 1) {
                    $version = $group['versions'][0];
                    $result['v' . $version['tag']] = Yii::t(
                        'app',
                        'Version {0}',
                        Yii::t('app-version2', $version['name']),
                    );
                } elseif ($n > 1) {
                    $result['~v' . $group['tag']] = Yii::t(
                        'app',
                        'Version {0}',
                        Yii::t('app-version2', $group['name']),
                    );
                    usort($group['versions'], fn (array $a, array $b): int => version_compare($b['tag'], $a['tag']));
                    foreach ($group['versions'] as $i => $version) {
                        $isLast = ($i === $n - 1);
                        $result['v' . $version['tag']] = sprintf(
                            '%s %s',
                            $isLast ? '┗' : '┣',
                            Yii::t(
                                'app',
                                'Version {0}',
                                Yii::t('app-version2', $version['name']),
                            ),
                        );
                    }
                }
            }
            return $result;
        })();
        $list = array_merge($list, $versions);

        return (string)$form->field($this->filter, 'term')->dropDownList($list)->label(false);
    }

    protected function drawTermPeriod(ActiveForm $form): string
    {
        $divId = $this->getId() . '-term';
        BootstrapDateTimePickerAsset::register($this->view);
        $this->view->registerCss("#{$divId}{margin-left:5%}");
        $this->view->registerJs(implode('', [
            "(function(\$){",
                "\$('#{$divId} input').datetimepicker({",
                    "format: 'YYYY-MM-DD HH:mm:ss'",
                "});",
                "\$('#filter-term').change(function(){",
                    "if($(this).val()==='term'){",
                        "\$('#{$divId}').show();",
                    "}else{",
                        "\$('#{$divId}').hide();",
                    "}",
                "}).change();",
            "})(jQuery);",
        ]));
        return Html::tag(
            'div',
            implode('', [
                $form->field($this->filter, 'term_from', [
                    'inputTemplate' => Yii::t(
                        'app',
                        '<div class="input-group"><span class="input-group-addon">From:</span>{input}</div>',
                    ),
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false),
                $form->field($this->filter, 'term_to', [
                    'inputTemplate' => Yii::t(
                        'app',
                        '<div class="input-group"><span class="input-group-addon">To:</span>{input}</div>',
                    ),
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false),
            ]),
            ['id' => $divId],
        );
    }
}
