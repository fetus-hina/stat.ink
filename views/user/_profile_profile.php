<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use statink\yii2\jdenticon\Jdenticon;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var User $user
 * @var View $this
 */

$f = Yii::$app->formatter;
?>
<?= DetailView::widget([
  'model' => $user,
  'formatter' => $f,
  'options' => [
    'class' => 'table table-striped',
  ],
  'attributes' => [
    [
      'attribute' => 'id',
      'value' => sprintf(
        '#%s (%s)',
        $f->asInteger($user->id),
        $f->asDateTime($user->join_at, 'short')
      ),
    ],
    [
      'label' => Yii::t('app', 'Icon'),
      'format' => 'raw',
      'value' => function () use ($user): string {
        // {{{
        $icon = null;
        $text = null;
        if ($user->userIcon) {
          $icon = Html::img($user->userIcon->url, ['width' => 48, 'height' => 48]);
        } else {
          $icon = Jdenticon::widget([
            'hash' => $user->identiconHash,
            'class' => 'identicon',
            'size' => 48,
          ]);
          $text = Yii::t('app', 'Auto (Identicon)');
        }
        return Html::tag(
          'div',
          implode('', [
            Html::tag('span', $icon, ['class' => 'profile-icon']),
            Html::tag('span', Html::encode((string)$text), ['class' => 'profile-icon-text']),
            Html::a(
              implode('', [
                Html::tag('span', '', ['class' => 'far fa-fw fa-image']),
                Html::encode(Yii::t('app', 'Change Icon')),
              ]),
              ['edit-icon'],
              ['class' => 'btn btn-default']
            ),
          ]),
          ['class' => 'profile-icon-container']
        );
        // }}}
      },
    ],
    [
      'attribute' => 'name',
    ],
    [
      'attribute' => 'screen_name',
      'label' => Yii::t('app', 'Screen Name'),
      'format' => 'raw',
      'value' => Html::tag('code', Html::encode($user->screen_name)),
    ],
    [
      'attribute' => 'password',
      'format' => 'raw',
      'value' => function () : string {
        return implode(' ', [
          Html::tag('code', Html::encode(str_repeat('*', 10))),
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fas fa-fw fa-redo']),
              Html::encode(Yii::t('app', 'Change Password')),
            ]),
            ['edit-password'],
            ['class' => 'btn btn-default']
          ),
        ]);
      },
    ],
    [
      'attribute' => 'api_key',
      'format' => 'raw',
      'value' => $this->render('_profile_apikey', ['user' => $user]),
    ],
    [
      'attribute' => 'email',
      'format' => 'raw',
      'value' => $this->render('_profile_email', ['user' => $user]),
    ],
    [
      'label' => implode(' ', [
        Yii::t('app', 'Black out other players (images)'),
        Yii::t('app', '(For only Splatoon 1 at this time)'),
      ]),
      'format' => 'raw',
      'value' => $this->render('_profile_blackout', [
        'conf' => $user->blackout,
        'id' => 'blackout-info',
      ]),
    ],
    [
      'label' => Yii::t('app', 'Black out other players (details)'),
      'format' => 'raw',
      'value' => $this->render('_profile_blackout', [
        'conf' => $user->blackout_list,
        'id' => 'blackout-info2',
        'mode' => 'splatoon2',
      ]),
    ],
    [
      'label' => Yii::t('app', "Link from other user's results"),
      'value' => Yii::t('app', $user->linkMode->name),
    ],
    [
      'label' => Yii::t('app', 'Region (used for Splatfest)'),
      'value' => Yii::t('app-region', $user->region->name),
    ],
    [
      'label' => Yii::t('app', 'Language (used for OStatus)'),
      'value' => Html::encode(implode(' / ', [
        $user->defaultLanguage->name,
        $user->defaultLanguage->name_en,
      ])),
    ],
    [
      'attribute' => 'nnid',
      'visible' => (trim((string)$user->nnid) !== ''),
    ],
    [
      'attribute' => 'sw_friend_code',
      'visible' => (trim((string)$user->sw_friend_code) !== ''),
      'value' => function () use ($user) : string {
        $id = trim((string)$user->sw_friend_code);
        return implode('-', [
          'SW',
          substr($id, 0, 4),
          substr($id, 4, 4),
          substr($id, 8, 4),
        ]);
      },
    ],
    [
      'attribute' => 'twitter',
      'visible' => (trim((string)$user->twitter) !== ''),
      'format' => 'raw',
      'value' => function () use ($user): string {
        TwitterWebIntentsAsset::register($this);
        return Html::a(
          implode(' ', [
            Icon::twitter(),
            '@' . Html::encode($user->twitter),
          ]),
          'https://twitter.com/intent/user?' . http_build_query([
            'screen_name' => $user->twitter,
          ])
        );
      },
    ],
    [
      'attribute' => 'ikanakama2',
      'visible' => (trim((string)$user->ikanakama2) !== ''),
      'format' => 'raw',
      'value' => function () use ($user): string {
        return Html::a(
          '#' . Html::encode($user->ikanakama2),
          sprintf('https://ikanakama.ink/users/%s', rawurlencode((string)$user->ikanakama2))
        );
      },
    ],
    [
      'label' => Yii::t('app', 'Capture Environment'),
      'attribute' => 'env.text',
      'format' => 'ntext',
    ],
  ],
]) ?>
<?php
$this->registerCss(<<<'CSS'
tbody th{width:10em}
.profile-icon-container{display:flex;flex-direction:row;flex-wrap:wrap;align-items:baseline}
.profile-icon{align-self:center;display:inline-block;border:1px solid #ccc;border-radius:4px;background-color:#fff;margin-right:1ex;line-height:1px}
.profile-icon-text{margin-right:1ex}
CSS
);
$this->registerJs(<<<'JS'
(function($){
  "use strict";
  $('#apikey-button').click(function () {
    $(this).hide();
    $('#apikey').show();
  });
})(jQuery);
JS
);
?>
