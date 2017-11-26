<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use app\models\Language;
use hiqdev\assets\flagiconcss\FlagIconCssAsset;
use yii\base\Widget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ChangeLangDropdown extends Widget
{
    public $current = null;
    public $buttonOptions = [
        'class' => 'btn btn-default',
    ];
    public $dropdownOptions = [];

    public function init()
    {
        parent::init();
        BootstrapPluginAsset::register($this->view);
        FlagIconCssAsset::register($this->view);
        if ($this->current === null) {
            $this->current = Yii::$app->language;
        }
    }

    public function run()
    {
        $id = $this->id;
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'button',
                    implode('', [
                        Html::tag('span', '', ['class' => 'fa fa-fw fa-language']),
                        implode(' / ', [
                            'Switch Language',
                            '言語切替',
                        ]),
                        ' ',
                        Html::tag('span', '', ['class' => 'caret']),
                    ]),
                    ArrayHelper::merge(
                        [
                            'id' => $id . '-dropdown',
                            'type' => 'button',
                            'data' => [
                                'toggle' => 'dropdown',
                            ],
                            'aria-haspopup' => 'true',
                            'aria-expanded' => 'false',
                        ],
                        $this->buttonOptions
                    )
                ),
                Html::tag(
                    'ul',
                    implode('', array_map(
                        function (Language $lang) : string {
                            // {{{
                            return Html::tag(
                                'li',
                                Html::a(
                                    implode(' ', [
                                        Html::tag('span', '', [
                                            'class' => [
                                                'fa',
                                                'fa-fw',
                                                $lang->lang === $this->current
                                                    ? 'fa-check'
                                                    : '',
                                            ],
                                        ]),
                                        Html::tag('span', '', [
                                            'class' => [
                                                'flag-icon',
                                                'flag-icon-' . $lang->countryCode,
                                            ],
                                        ]),
                                        Html::encode(
                                            $lang->name .
                                            ' / ' .
                                            $lang->name_en
                                        ),
                                    ]),
                                    'javascript:;',
                                    [
                                        'class' => [
                                            'language-change',
                                        ],
                                        'data' => [
                                            'lang' => $lang->lang,
                                        ],
                                    ]
                                )
                            );
                            // }}}
                        },
                        Language::find()->orderBy(['name' => SORT_ASC])->all()
                    )),
                    ArrayHelper::merge(
                        [
                            'class' => 'dropdown-menu',
                            'aria-labelledby' => $id . '-dropdown',
                        ],
                        $this->dropdownOptions
                    )
                ),
            ]),
            [
                'id' => $id,
                'class' => 'dropdown',
            ]
        );
    }
}
