<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets;

use Yii;
use app\components\helpers\Resource;
use app\components\helpers\db\Now;
use app\models\GameMode;
use app\models\Lobby;
use app\models\Map;
use app\models\RankGroup;
use app\models\Rule;
use app\models\Special;
use app\models\SplatoonVersion;
use app\models\Subweapon;
use app\models\User;
use app\models\UserWeapon;
use app\models\Weapon;
use app\models\WeaponType;
use jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

final class BattleFilterWidget extends Widget
{
    public $id = 'filter-form';
    public $route;
    public $screen_name;
    public $filter;

    public $lobby = true;
    public $rule = true;
    public $map = true;
    public $weapon = true;
    public $rank = true;
    public $result = true;
    public $term = true;
    public $action = 'search'; // search or summarize

    public function run()
    {
        ob_start();
        $cleaner = new Resource(true, function () {
            ob_end_clean();
        });
        $divId = $this->getId();
        $this->view->registerCss("#{$divId}{border:1px solid #ccc;border-radius:5px;padding:15px;margin-bottom:15px}");
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
    }

    protected function drawFields(ActiveForm $form)
    {
        $ret = [];
        if ($this->lobby) {
            $ret[] = $this->drawLobby($form);
        }
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
        if ($this->term) {
            $ret[] = $this->drawTerm($form);
            $ret[] = Html::hiddenInput(
                sprintf('%s[%s]', $this->filter->formName(), 'timezone'),
                Yii::$app->timeZone,
            );
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

    protected function drawLobby(ActiveForm $form)
    {
        $list = (function () {
            $ret = ['' => Yii::t('app-rule', 'Any Lobby')];
            foreach (Lobby::find()->orderBy('[[id]] ASC')->asArray()->all() as $a) {
                $ret[$a['key']] = Yii::t('app-rule', $a['name']);
            }
            return $ret;
        })();
        return $form->field($this->filter, 'lobby')->dropDownList($list)->label(false);
    }

    protected function drawRule(ActiveForm $form)
    {
        $list = (function () {
            $ret = ['' => Yii::t('app-rule', 'Any Mode')];
            foreach (GameMode::find()->orderBy('[[id]] ASC')->asArray()->all() as $gameMode) {
                $gameModeText = Yii::t('app-rule', $gameMode['name']); // "ナワバリバトル"
                $rules = Rule::find()
                    ->andWhere(['mode_id' => $gameMode['id']])
                    ->orderBy('[[id]] ASC')
                    ->asArray()
                    ->all();
                $mode = [];
                if (count($rules) > 1) {
                    $mode['@' . $gameMode['key']] = Yii::t('app-rule', 'All of {0}', $gameModeText);
                }
                foreach ($rules as $rule) {
                    $mode[$rule['key']] = Yii::t('app-rule', $rule['name']);
                }
                $ret[$gameModeText] = $mode;
            }
            return $ret;
        })();
        return $form->field($this->filter, 'rule')->dropDownList($list)->label(false);
    }

    protected function drawMap(ActiveForm $form)
    {
        $list = (fn () => array_merge(
            ['' => Yii::t('app-map', 'Any Stage')],
            (function () {
                    $ret = [];
                    foreach (Map::find()->asArray()->all() as $map) {
                        $ret[$map['key']] = Yii::t('app-map', $map['name']);
                    }
                    uasort($ret, 'strnatcasecmp');
                    return $ret;
            })(),
        ))();
        return $form->field($this->filter, 'map')->dropDownList($list)->label(false);
    }

    protected function drawWeapon(ActiveForm $form)
    {
        $user = User::findOne(['screen_name' => $this->screen_name]);
        $weaponIdList = $this->getUsedWeaponIdList($user);
        $list = array_merge(
            $this->createMainWeaponList($weaponIdList),
            $this->createGroupedMainWeaponList($weaponIdList),
            $this->createSubWeaponList($weaponIdList),
            $this->createSpecialWeaponList($weaponIdList),
        );
        return $form->field($this->filter, 'weapon')->dropDownList($list)->label(false);
    }

    /**
     * @return int[]|null
     */
    protected function getUsedWeaponIdList(?User $user = null): ?array
    {
        if (!$user) {
            return null;
        }

        return ArrayHelper::getColumn(
            UserWeapon::find()
                ->andWhere(['user_id' => $user->id])
                ->all(),
            fn (UserWeapon $model): int => (int)$model->weapon_id,
        );
    }

    protected function createMainWeaponList(array $weaponIdList)
    {
        $ret = [];
        $types = WeaponType::find()->orderBy('[[id]] ASC')->asArray()->all();
        foreach ($types as $type) {
            $typeName = Yii::t('app-weapon', $type['name']);

            $tmp = [];
            $weapons = Weapon::find()
                ->andWhere([
                    '{{weapon}}.[[type_id]]' => $type['id'],
                    '{{weapon}}.[[id]]' => $weaponIdList,
                ])
                ->asArray()
                ->all();
            foreach ($weapons as $weapon) {
                $tmp[$weapon['key']] = Yii::t('app-weapon', $weapon['name']);
            }
            if (count($tmp) > 1) {
                uasort($tmp, 'strnatcasecmp');
                $ret[$typeName] = array_merge(
                    ['@' . $type['key'] => Yii::t('app-weapon', 'All of {0}', $typeName)],
                    $tmp,
                );
            } elseif (count($tmp) === 1) {
                $ret[$typeName] = $tmp;
            }
        }
        return array_merge(
            ['' => Yii::t('app-weapon', 'Any Weapon')],
            $ret,
        );
    }

    protected function createGroupedMainWeaponList(array $weaponIdList)
    {
        return [
            Yii::t('app', 'Main Weapon') => (function () use ($weaponIdList) {
                $ret = [];
                $subQuery = (new \yii\db\Query())
                    ->select(['id' => '{{weapon}}.[[main_group_id]]'])
                    ->from('weapon')
                    ->andWhere(['in', '{{weapon}}.[[id]]', $weaponIdList]);
                $list = Weapon::find()
                    ->andWhere(['{{weapon}}.[[id]]' => $subQuery])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['~' . $item['key']] = Yii::t('app', '{0} etc.', Yii::t('app-weapon', $item['name']));
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })(),
        ];
    }

    protected function createSubWeaponList(array $weaponIdList)
    {
        return [
            Yii::t('app', 'Sub Weapon') => (function () use ($weaponIdList) {
                $ret = [];
                $list = SubWeapon::find()
                    ->innerJoinWith('weapons')
                    ->andWhere(['{{weapon}}.[[id]]' => $weaponIdList])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['+' . $item['key']] = Yii::t('app-subweapon', $item['name']);
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
                $list = Special::find()
                    ->innerJoinWith('weapons')
                    ->andWhere(['{{weapon}}.[[id]]' => $weaponIdList])
                    ->asArray()
                    ->all();
                foreach ($list as $item) {
                    $ret['*' . $item['key']] = Yii::t('app-special', $item['name']);
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })(),
        ];
    }

    protected function drawRank(ActiveForm $form)
    {
        $groups = RankGroup::find()
            ->with([
                'ranks' => fn ($q) => $q->orderBy('[[id]] DESC'),
            ])
            ->orderBy('[[id]] DESC')
            ->asArray()
            ->all();

        $list = [];
        $list[''] = Yii::t('app-rank', 'Any Rank');
        foreach ($groups as $group) {
            $list['~' . $group['key']] = Yii::t('app-rank', $group['name']);
            foreach ($group['ranks'] as $i => $rank) {
                $list[$rank['key']] = sprintf(
                    '%s %s',
                    ($i !== count($group['ranks']) - 1 ? '├' : '└'),
                    Yii::t('app-rank', $rank['name']),
                );
            }
        }
        return $form->field($this->filter, 'rank')->dropDownList($list)->label(false);
    }

    protected function drawResult(ActiveForm $form)
    {
        $list = [
            ''      => Yii::t('app', 'Won / Lost'),
            'win'   => Yii::t('app', 'Won'),
            'lose'  => Yii::t('app', 'Lost'),
        ];
        return $form->field($this->filter, 'result')->dropDownList($list)->label(false);
    }

    protected function drawTerm(ActiveForm $form)
    {
        return $this->drawTermMain($form) . $this->drawTermPeriod($form);
    }

    protected function drawTermMain(ActiveForm $form)
    {
        $list = [
            ''                  => Yii::t('app', 'Any Time'),
            'this-period'       => Yii::t('app', 'Current Period'),
            'last-period'       => Yii::t('app', 'Previous Period'),
            '24h'               => Yii::t('app', 'Last 24 Hours'),
            'today'             => Yii::t('app', 'Today'),
            'yesterday'         => Yii::t('app', 'Yesterday'),
            'last-10-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  10]),
            'last-20-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  20]),
            'last-50-battles'   => Yii::t('app', 'Last {n} Battles', ['n' =>  50]),
            'last-100-battles'  => Yii::t('app', 'Last {n} Battles', ['n' => 100]),
            'last-200-battles'  => Yii::t('app', 'Last {n} Battles', ['n' => 200]),
        ];

        $versions = SplatoonVersion::find()
            ->andWhere(['between', 'released_at', '2015-09-02T10:00:00+09:00', new Now()])
            ->asArray()
            ->all();
        usort($versions, fn ($a, $b) => version_compare($a['tag'], $b['tag']));
        foreach ($versions as $version) {
            $list['v' . $version['tag']] = Yii::t('app', 'Version {0}', $version['name']);
        }

        $list['term'] = Yii::t('app', 'Specify Period');

        return $form->field($this->filter, 'term')->dropDownList($list)->label(false);
    }

    protected function drawTermPeriod(ActiveForm $form)
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
