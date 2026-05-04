<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\CreateBattle3Asset;
use app\components\widgets\Dialog;
use app\components\widgets\Icon;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Weapon3;
use app\models\WeaponType3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

use function array_merge;
use function implode;
use function sprintf;

use const SORT_ASC;
use const SORT_LOCALE_STRING;
use const SORT_NATURAL;

final class CreateBattleWidget extends Dialog
{
    public function init()
    {
        parent::init();

        $this->title = Yii::t('app', 'Register a Battle Manually (Splatoon 3)');
        $this->hasClose = true;
        $this->footer = false;
        $this->size = self::SIZE_MEDIUM;
        $this->body = $this->createBody();
    }

    private function createBody(): string
    {
        CreateBattle3Asset::register($this->view);
        $this->view->registerJs(
            sprintf(
                'window.__createBattle3Config = %s;',
                Json::encode([
                    'apiKey' => (string)Yii::$app->user->identity?->api_key,
                    'apiUrl' => Url::to('@web/api/v3/battle', true),
                    'scheduleUrl' => Url::to('@web/api/internal/current-schedule3', true),
                    'agent' => sprintf('%s web client', (string)Yii::$app->name),
                    'agentVersion' => sprintf('v%s', (string)Yii::$app->version),
                    'i18n' => [
                        'success' => Yii::t('app', 'Registered.'),
                        'error' => Yii::t('app', 'Could not register the battle.'),
                    ],
                ]),
            ),
            View::POS_HEAD,
        );

        return Html::tag(
            'form',
            implode('', [
                $this->renderAlertBox(),
                $this->renderRefreshScheduleButton(),
                $this->renderTwoColumns(
                    $this->renderSelect('lobby', Yii::t('app', 'Lobby'), $this->makeLobbies()),
                    $this->renderSelect('rule', Yii::t('app', 'Mode'), $this->makeRules()),
                ),
                $this->renderStageField(),
                $this->renderSelect('weapon', Yii::t('app', 'Weapon'), $this->makeWeapons()),
                $this->renderResultButtonGroup(),
                $this->renderTwoColumns(
                    $this->renderNumberInput('kill_or_assist', Yii::t('app', 'Kill or Assist')),
                    $this->renderNumberInput('assist', Yii::t('app', 'Assists')),
                ),
                $this->renderTwoColumns(
                    $this->renderNumberInput('death', Yii::t('app', 'Deaths')),
                    $this->renderNumberInput('special', Yii::t('app', 'Specials')),
                ),
                $this->renderNumberInput(
                    'inked',
                    Yii::t('app', 'Turf Inked'),
                    null,
                ),
                Html::submitButton(
                    Html::encode(Yii::t('app', 'Submit Battle')),
                    [
                        'class' => 'btn btn-primary btn-block',
                        'id' => 'create-battle3-submit',
                    ],
                ),
            ]),
            [
                'id' => 'create-battle3-form',
                'novalidate' => 'novalidate',
            ],
        );
    }

    private function renderAlertBox(): string
    {
        return Html::tag(
            'div',
            '',
            [
                'id' => 'create-battle3-alert',
                'class' => ['alert'],
                'role' => 'alert',
                'style' => 'display:none',
            ],
        );
    }

    private function renderRefreshScheduleButton(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'button',
                implode('', [
                    Icon::refresh(),
                    ' ',
                    Html::encode(Yii::t('app', 'Refresh Schedule')),
                ]),
                [
                    'type' => 'button',
                    'class' => 'btn btn-default btn-sm',
                    'id' => 'create-battle3-refresh-schedule',
                ],
            ),
            [
                'class' => 'text-right',
                'style' => 'margin-bottom:10px',
            ],
        );
    }

    private function renderTwoColumns(string $left, string $right): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag('div', $left, ['class' => 'col-sm-6']),
                Html::tag('div', $right, ['class' => 'col-sm-6']),
            ]),
            ['class' => 'row'],
        );
    }

    private function renderStageField(): string
    {
        $id = 'create-battle3-stage';
        return Html::tag(
            'div',
            implode('', [
                Html::label(
                    Html::encode(Yii::t('app', 'Stage')),
                    $id,
                    ['class' => 'control-label'],
                ),
                Html::tag(
                    'div',
                    Html::dropDownList('stage', null, $this->makeMaps(), [
                        'class' => 'form-control',
                        'id' => $id,
                    ]),
                    ['id' => 'create-battle3-stage-control'],
                ),
            ]),
            ['class' => 'form-group'],
        );
    }

    /**
     * @param array<string, string|array<string, string>> $options
     */
    private function renderSelect(string $name, string $label, array $options): string
    {
        $id = sprintf('create-battle3-%s', $name);
        return Html::tag(
            'div',
            implode('', [
                Html::label(Html::encode($label), $id, ['class' => 'control-label']),
                Html::dropDownList($name, null, $options, [
                    'class' => 'form-control',
                    'id' => $id,
                ]),
            ]),
            ['class' => 'form-group'],
        );
    }

    private function renderNumberInput(
        string $name,
        string $label,
        ?int $max = 99,
    ): string {
        $id = sprintf('create-battle3-%s', $name);
        $options = [
            'class' => 'form-control',
            'id' => $id,
            'min' => 0,
            'step' => 1,
            'inputmode' => 'numeric',
        ];
        if ($max !== null) {
            $options['max'] = $max;
        }

        return Html::tag(
            'div',
            implode('', [
                Html::label(Html::encode($label), $id, ['class' => 'control-label']),
                Html::input('number', $name, null, $options),
            ]),
            ['class' => 'form-group'],
        );
    }

    /**
     * @return array<string, string>
     */
    private function makeLobbies(): array
    {
        return array_merge(
            ['' => Yii::t('app', 'Unknown')],
            ArrayHelper::map(
                Lobby3::find()->orderBy(['rank' => SORT_ASC])->all(),
                'key',
                fn (Lobby3 $lobby): string => Yii::t('app-lobby3', $lobby->name),
            ),
        );
    }

    /**
     * @return array<string, string>
     */
    private function makeRules(): array
    {
        return array_merge(
            ['' => Yii::t('app', 'Unknown')],
            ArrayHelper::map(
                Rule3::find()->orderBy(['rank' => SORT_ASC])->all(),
                'key',
                fn (Rule3 $rule): string => Yii::t('app-rule3', $rule->name),
            ),
        );
    }

    /**
     * @return array<string, string>
     */
    private function makeMaps(): array
    {
        return array_merge(
            ['' => Yii::t('app', 'Unknown')],
            ArrayHelper::asort(
                ArrayHelper::map(
                    Map3::find()->all(),
                    'key',
                    fn (Map3 $map): string => Yii::t('app-map3', $map->name),
                ),
                SORT_NATURAL,
            ),
        );
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    private function makeWeapons(): array
    {
        $weapons = ArrayHelper::map(
            Weapon3::find()->with(['mainweapon'])->all(),
            'key',
            fn (Weapon3 $model): string => Yii::t('app-weapon3', $model->name),
            fn (Weapon3 $model): int => (int)$model->mainweapon->type_id,
        );

        $result = ['' => Yii::t('app', 'Unknown')];
        foreach (WeaponType3::find()->orderBy(['rank' => SORT_ASC])->all() as $type) {
            $typeWeapons = ArrayHelper::asort(
                ArrayHelper::getValue($weapons, $type->id, []),
                SORT_LOCALE_STRING,
            );
            if ($typeWeapons !== []) {
                $result[Yii::t('app-weapon3', $type->name)] = $typeWeapons;
            }
        }

        return $result;
    }

    private function renderResultButtonGroup(): string
    {
        $options = [
            ['', Yii::t('app', 'Unknown')],
            ['win', Yii::t('app', 'Won')],
            ['lose', Yii::t('app', 'Lost')],
            ['exempted_lose', Yii::t('app', 'Disconnected')],
        ];

        $buttons = [];
        foreach ($options as [$value, $label]) {
            $isDefault = $value === '';
            $buttons[] = Html::tag(
                'label',
                implode('', [
                    Html::radio('result', $isDefault, [
                        'value' => $value,
                        'autocomplete' => 'off',
                    ]),
                    Html::encode($label),
                ]),
                [
                    'class' => [
                        'btn',
                        $isDefault ? 'btn-primary' : 'btn-default',
                        $isDefault ? 'active' : '',
                    ],
                ],
            );
        }

        return Html::tag(
            'div',
            implode('', [
                Html::label(
                    Html::encode(Yii::t('app', 'Result')),
                    null,
                    ['class' => 'control-label d-block'],
                ),
                Html::tag(
                    'div',
                    implode('', $buttons),
                    [
                        'class' => 'btn-group btn-group-justified',
                        'data-toggle' => 'buttons',
                        'role' => 'group',
                    ],
                ),
            ]),
            ['class' => 'form-group'],
        );
    }
}
