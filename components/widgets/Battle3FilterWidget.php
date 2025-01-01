<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\models\Battle3FilterForm;
use app\models\Battle3PlayedWith;
use app\models\User;
use jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset;
use yii\base\Widget;
use yii\bootstrap\ActiveField;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

use function implode;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function sprintf;
use function str_replace;
use function vsprintf;

final class Battle3FilterWidget extends Widget
{
    public $id = 'filter-form';
    public ?string $route = null;
    public ?User $user = null;
    public ?Battle3FilterForm $filter = null;

    public bool $lobby = true;
    public bool $rule = true;
    public bool $map = true;
    public bool $weapon = true;
    public bool $rank = true; // not impl. yet
    public bool $result = true;
    public bool $knockout = true;
    public bool $connectivity = false; // not impl. yet
    public bool $term = true;
    public bool $playedWith = false;
    // public bool $filterText = false;
    // public bool $withTeam = false;
    public string $action = 'search'; // search or summarize

    public function run(): string
    {
        ob_start();
        try {
            $divId = $this->getId();
            $view = $this->view;
            if ($view instanceof View) {
                $view->registerCss(vsprintf('#%s{%s}', [
                    $divId,
                    Html::cssStyleFromArray([
                        'border' => '1px solid #ccc',
                        'border-radius' => '5px',
                        'padding' => '15px',
                        'margin-bottom' => '15px',
                    ]),
                ]));
            }

            echo Html::beginTag('div', ['id' => $divId]);
            $form = ActiveForm::begin([
                'id' => $this->id,
                'action' => [$this->route,
                    'screen_name' => $this->user ? $this->user->screen_name : null,
                ],
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
        $filter = $this->filter ?: Yii::createObject(Battle3FilterForm::class);
        $filter->updateUnixtimeToString();

        return implode('', [
            $this->drawLobby($form, $filter),
            $this->drawRule($form, $filter),
            $this->drawMap($form, $filter),
            $this->drawWeapon($form, $filter),
            $this->drawResult($form, $filter),
            $this->drawKnockout($form, $filter),
            $this->drawTerm($form, $filter),
            $this->drawPlayedWith($form, $filter),
            $this->drawActionButton($this->action),
        ]);
    }

    protected function drawLobby(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->lobby) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'lobby')
                ->dropDownList(...$filter->getLobbyDropdown())
                ->label(false),
        );
    }

    protected function drawRule(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->rule) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'rule')
                ->dropDownList(...$filter->getRuleDropdown())
                ->label(false),
        );
    }

    protected function drawMap(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->map) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'map')
                ->dropDownList(...$filter->getMapDropdown())
                ->label(false),
        );
    }

    protected function drawWeapon(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->weapon) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'weapon')
                ->dropDownList(...$filter->getWeaponDropdown($this->user))
                ->label(false),
        );
    }

    protected function drawResult(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->result) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'result', [
                    'inputTemplate' => Html::tag(
                        'div',
                        implode('', [
                            '{input}',
                            Html::a(
                                Icon::help(),
                                'https://github.com/fetus-hina/stat.ink/wiki/Splatoon-3-Result-Filter',
                                [
                                    'target' => '_blank',
                                    'rel' => 'noopener',
                                    'class' => 'input-group-addon',
                                ],
                            ),
                        ]),
                        ['class' => 'input-group'],
                    ),
                ])
                ->dropDownList(...$filter->getResultDropdown())
                ->label(false),
        );
    }

    protected function drawKnockout(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->knockout) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'knockout')
                ->dropDownList(...$filter->getKnockoutDropdown())
                ->label(false),
        );
    }

    protected function drawTerm(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->term) {
            return '';
        }

        return implode('', [
            $this->drawTermDropdown($form, $filter),
            $this->drawTermSpecify($form, $filter),
        ]);
    }

    private function drawTermDropdown(ActiveForm $form, Battle3FilterForm $filter): string
    {
        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'term')
                ->dropDownList(...$filter->getTermDropdown())
                ->label(false),
        );
    }

    private function drawTermSpecify(ActiveForm $form, Battle3FilterForm $filter): string
    {
        $divId = sprintf('%s-term', (string)$this->getId());

        BootstrapDateTimePickerAsset::register($this->view);

        // (function ($) {
        //   $('#{divId} input')
        //     .datetimepicker({
        //       format: 'YYYY-MM-DD HH:mm:ss'
        //     });
        //
        //   $('#f-term')
        //     .change(
        //       function () {
        //         if ($(this).val() === 'term') {
        //           $('#{divId}').show();
        //         } else {
        //           $('#{divId}').hide();
        //         }
        //       }
        //     )
        //     .change();
        // })(jQuery);

        $this->view->registerJs(
            str_replace(
                '{divId}',
                $divId,
                '!function(i){i("#{divId} input").datetimepicker({format:"YYYY-MM-DD HH:mm:ss"}),i("#f-term").change(function(){"term"===i(this).val()?i("#{divId}").show():i("#{divId}").hide()}).change()}(jQuery);',
            ),
        );

        return Html::tag(
            'div',
            implode(
                '',
                [
                    (string)self::disableClientValidation(
                        $form
                            ->field(
                                $filter,
                                'term_from',
                                [
                                    'inputTemplate' => Yii::t('app', '<div class="input-group"><span class="input-group-addon">From:</span>{input}</div>'),
                                ],
                            )
                            ->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])
                            ->label(false),
                    ),
                    (string)self::disableClientValidation(
                        $form
                            ->field(
                                $filter,
                                'term_to',
                                [
                                    'inputTemplate' => Yii::t('app', '<div class="input-group"><span class="input-group-addon">To:</span>{input}</div>'),
                                ],
                            )
                            ->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])
                            ->label(false),
                    ),
                ],
            ),
            [
                'id' => $divId,
                'class' => 'ml-3',
            ],
        );
    }

    private function drawPlayedWith(ActiveForm $form, Battle3FilterForm $filter): string
    {
        if (!$this->playedWith) {
            return '';
        }

        return (string)self::disableClientValidation(
            $form
                ->field($filter, 'played_with')
                ->dropDownList(
                    ...$filter->getPlayedWithDropdown(
                        $this->user,
                        $filter->played_with && !$filter->hasErrors('played_with')
                            ? Battle3PlayedWith::findOne([
                                'user_id' => $this->user->id,
                                'ref_id' => $filter->played_with,
                            ])
                            : null,
                    ),
                )
                ->label(false),
        );
    }

    private function drawActionButton(string $action): string
    {
        if ($action === 'summarize') {
            return Html::tag(
                'button',
                implode(' ', [
                    Icon::filter(),
                    Html::encode(Yii::t('app', 'Summarize')),
                ]),
                [
                    'type' => 'submit',
                    'class' => [
                        'btn',
                        'btn-primary',
                    ],
                ],
            );
        }

        return Html::tag(
            'button',
            implode(' ', [
                Icon::search(),
                Html::encode(Yii::t('app', 'Search')),
            ]),
            [
                'type' => 'submit',
                'class' => ['btn', 'btn-primary'],
            ],
        );
    }

    private static function disableClientValidation(ActiveField $field): ActiveField
    {
        $field->enableClientValidation = false;
        return $field;
    }
}
