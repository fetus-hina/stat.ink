<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Battle;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Battle $battle
 * @var View $this
 */

if (!$effects = $battle->abilityEffects) {
  return '';
}

$f = Yii::$app->formatter;

$percent = function ($value, $number = 1) use ($f) : string {
  if ($value === null) {
    return '';
  }

  return $f->asPercent($value, 1);
};
?>
<h2 id="effect"><?= Html::encode(Yii::t('app', 'Ability Effect')) ?></h2>
<?= DetailView::widget([
  'model' => $effects,
  'template' => function ($attribute, $index, $widget) : string {
    $value = trim($widget->formatter->format($attribute['value'], $attribute['format']));
    if ($value === '') {
      return '';
    }
    $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
    $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));
    $template = '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';
    return strtr($template, [
      '{label}' => Html::encode($attribute['label']),
      '{value}' => $widget->formatter->format($attribute['value'], $attribute['format']),
      '{captionOptions}' => $captionOptions,
      '{contentOptions}' => $contentOptions,
    ]);
  },
  'options' => ['class' => ['table table-striped detail-view']],
  'attributes' => [
    [
      'label' => Yii::t('app-gearstat', 'Damage'),
      'format' => 'raw',
      'value' => $this->render('_battle_ability_effect_attack', ['battle' => $battle]),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Defense'),
      'value' => $percent($effects->defensePct),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Ink Usage(Main)'),
      'value' => $percent($effects->inkUsePctMain),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Ink Usage(Sub)'),
      'value' => $percent($effects->inkUsePctSub),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Ink Recovery'),
      'value' => function ($effects) use ($f) {
        $sec = $effects->inkRecoverySec;
        if ($sec === null) {
          return '';
        }
        return Yii::t('app', '{sec} seconds ({pct} %)', [
          'sec' => $f->asDecimal($sec, 2),
          'pct' => $f->asDecimal(300 / $sec, 1),
        ]);
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Run Speed'),
      'value' => $percent($effects->runSpeedPct),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Swim Speed'),
      'value' => function ($effects) use ($f, $battle) {
        $pct = $effects->swimSpeedPct;
        if ($pct === null) {
          return '';
        }

        if (!$weapon = $battle->weapon) {
          return $f->asPercent($pct, 1);
        }

        return $f->asPercent($pct, 1) . ' (' . Yii::t('app-weapon', $weapon->name) . ')';
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Special Charge'),
      'value' => function ($effects) use ($f, $battle) {
        $value = $effects->specialChargePoint;
        if ($value === null) {
          return '';
        }

        if (!$weapon = $battle->weapon) {
          return $f->asInteger($value);
        }

        return sprintf('%s p (%s)', $f->asInteger($value), Yii::t('app-special', $weapon->special->name));
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Special Duration'),
      'value' => function ($effects) use ($f, $battle) {
        $value = $effects->specialDurationSec;
        if ($value === null) {
          return '';
        }

        $value2 = $effects->specialDurationCount;
        $weapon = $battle->weapon;
        return implode(' ', array_filter([
          ($value2 === null)
            ? Yii::t('app', '{sec} seconds', [
              'sec' => $f->asDecimal($value, 2),
            ])
            : Yii::t('app', '{sec} seconds, {cnt} times', [
              'sec' => $f->asDecimal($value, 2),
              'cnt' => $f->asInteger($value2),
            ]),
          $weapon
            ? '(' . Yii::t('app-special', $weapon->special->name) . ')'
            : null
        ]));
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Special Save'),
      'value' => function ($effects) use ($f, $battle) {
        $value = $effects->specialLossPct;
        if ($value === null) {
          return '';
        }

        $weapon = $battle->weapon;
        return implode(' ', array_filter([
          Yii::t('app', '{pct} % loss', [
            'pct' => $f->asDecimal($value * 100, 1),
          ]),
          $weapon
            ? '(' . Yii::t('app-weapon', $weapon->name) . ')'
            : null
        ]));
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Respawn'),
      'value' => $effects->respawnSec !== null
        ? Yii::t('app', '{sec} seconds', ['sec' => $f->asDecimal($effects->respawnSec, 2)])
        : '',
    ],
    [
      'label' => Yii::t('app-gearstat', 'Jump'),
      'format' => 'raw',
      'value' => function ($effects) use ($f) {
        $values = $effects->superJumpSecs;
        if ($values === null) {
          return '';
        }

        $_ = function (float $sec, ?string $label) use ($f) : string {
          return Html::tag(
            'span',
            Html::encode(Yii::t('app', '{sec} seconds', ['sec' => $f->asDecimal($sec, 2)])),
            $label !== null
              ? ['class' => 'auto-tooltip', 'title' => $label]
              : []
          );
        };

        return vsprintf('%s + %s + %s + %s = %s', [
          $_($values['prepare'], Yii::t('app-gearstat', 'Prepare')),
          $_($values['pullup'], Yii::t('app-gearstat', 'Ascent')),
          $_($values['pulldown'], Yii::t('app-gearstat', 'Descent')),
          $_($values['rigid'], Yii::t('app-gearstat', 'Stiffen')),
          $_(
            $values['prepare'] + $values['pullup'] + $values['pulldown'] + $values['rigid'],
            null
          ),
        ]);
      },
    ],
    [
      'label' => Yii::t('app-gearstat', 'Bomb Throw'),
      'value' => $percent($effects->bombThrowPct),
    ],
    [
      'label' => Yii::t('app-gearstat', 'Echolocator'),
      'value' => $percent($effects->markingPct),
    ],
  ],
]) . "\n" ?>
<p class="text-right" style="font-size:10px;line-height:1.1">
  [<?= Html::encode($effects->calculatorVersion) ?>]<br>
  Powered by <?= Html::a(
    Html::encode('ギアパワー検証 - スプラトゥーン(Splatoon) for 2ch Wiki*'),
    'http://wikiwiki.jp/splatoon2ch/?%A5%AE%A5%A2%A5%D1%A5%EF%A1%BC%B8%A1%BE%DA'
  ) . "\n" ?>
</p>
