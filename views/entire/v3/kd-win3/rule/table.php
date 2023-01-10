<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\Rule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 * @var array<int, array<int, array{battles: int, wins: int}>> $data
 * @var string|null $lobbyKey
 */

if ($rule->key === 'nawabari') {
  if (
    $lobbyKey === 'xmatch' ||
    $lobbyKey === 'league' ||
    str_starts_with((string)$lobbyKey, 'bankara_')
  ) {
    echo Html::tag('p', Html::encode(Yii::t('app', 'N/A')));
    return;
  }
} else {
  if ($lobbyKey === 'regular' || str_starts_with((string)$lobbyKey, 'splatfest')) {
    echo Html::tag('p', Html::encode(Yii::t('app', 'N/A')));
    return;
  }
}

TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;

$width = 100.0 / (20 + 1);

$this->registerCss(
  Html::renderCss([
    '.table-responsive .rule-table td,.table-responsive .rule-table th' => [
      'width' => "{$width}%",
      'min-width' => '2.5em',
    ],
  ]),
);

$this->registerCss(
  '@media screen and (min-width:768px){.table-responsive table.rule-table{table-layout:fixed}}',
);

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-bordered table-condensed rule-table m-0">
    <thead>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(
            vsprintf('%s＼%s', [
              Yii::t('app', 'd'),
              Yii::t('app', 'k'),
            ]),
          ),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= implode('', array_map(
          fn (int $k): string => Html::tag(
            'th',
            Html::encode(
              implode('', [
                $fmt->asInteger($k),
                $k === 20 ? '+' : '',
              ]),
            ),
            [
              'class' => [
                'text-center',
                'kdcell',
              ],
            ],
          ),
          range(0, 20),
        )) . "\n" ?>
      </tr>
    </thead>
    <tbody>
<?php foreach (range(0, 20) as $d) { ?>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(
            implode('', [
              $fmt->asInteger($d),
              $d === 20 ? '+' : '',
            ]),
          ),
          [
            'class' => [
              'text-center',
              'kdcell',
            ],
            'scope' => 'row',
          ],
        ) . "\n" ?>
<?php foreach (range(0, 20) as $k) { ?>
        <?= $this->render('table/cell', [
          'battles' => ArrayHelper::getValue($data, [$d, $k, 'battles']),
          'wins' => ArrayHelper::getValue($data, [$d, $k, 'wins']),
        ]) . "\n" ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
