<?php

declare(strict_types=1);

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

$fmt = Yii::$app->formatter;

?>
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
        'battles' => (int)ArrayHelper::getValue($data, [$d, $k, 'battles']),
        'wins' => (int)ArrayHelper::getValue($data, [$d, $k, 'wins']),
      ]) . "\n" ?>
<?php } ?>
    </tr>
<?php } ?>
  </tbody>
</table>
