<?php
use app\models\Language;
use app\models\SupportLevel;
use hiqdev\assets\flagiconcss\FlagIconCssAsset;
use yii\helpers\Html;
?>
<?= Html::a(
  implode('', [
    Html::tag('span', '', ['class' => 'fa fa-fw fa-language']),
    Html::encode('Language'),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  'javascript:;',
  [
    'class' => 'dropdown-toggle',
    'data' => [
      'toggle' => 'dropdown',
    ],
    'role' => 'button',
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>
<?= Html::tag(
  'ul',
  implode('', array_merge(
    array_map(
      function (Language $lang) : string {
        FlagIconCssAsset::register($this);
        return Html::tag(
          'li',
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => [
                'fa',
                'fa-fw',
                Yii::$app->language === $lang->lang ? 'fa-check' : '',
              ]]),
              Html::tag('span', '', ['class' => [
                'flag-icon',
                'flag-icon-' . strtolower(substr($lang->lang, 3, 2)),
              ]]),
              ' ',
              Html::encode($lang->name),
              ' / ',
              Html::encode($lang->name_en),
              ' ',
              (function (SupportLevel $level) : string {
                switch ($level->id) {
                  case SupportLevel::FULL:
                  case SupportLevel::ALMOST:
                    return '';

                  case SupportLevel::PARTIAL:
                    return Html::tag(
                      'span',
                      Html::tag('span', '', ['class' => 'fas fa-fw fa-exclamation-circle']),
                      [
                        'class' => 'auto-tooltip',
                        'title' => 'Partially supported',
                      ]
                    );

                  case SupportLevel::FEW:
                    return Html::tag(
                      'span',
                      Html::tag('span', '', ['class' => 'fas fa-fw fa-exclamation-triangle']),
                      [
                        'class' => 'auto-tooltip',
                        'title' => 'Proper-noun only',
                      ]
                    );
                }
              })($lang->supportLevel),
            ]),
            'javascript:;',
            [
              'hreflang' => $lang->lang,
              'data' => [
                'lang' => $lang->lang,
              ],
              'class' => 'language-change',
            ]
          )
        );
      },
      Language::find()->with('supportLevel')->orderBy(['name' => SORT_ASC])->all()
    ),
    [
      Html::tag('li', '', ['class' => 'divider']),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-question-circle']),
          Html::encode(Yii::t('app', 'About Translation')),
        ]),
        ['/site/translate']
      )),
    ]
  )),
  ['class' => 'dropdown-menu']
) ?>
