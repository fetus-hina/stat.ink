<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\OverfishingTrait;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type OverfishingStats from OverfishingTrait
 *
 * @var OverfishingStats $stats
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonWaterLevel2> $tides
 * @var string $modalId
 */

$fmt = clone Yii::$app->formatter;
$fmt->nullDisplay = '';

?>
<?= Html::beginTag('div', [
  'class' => 'modal fade',
  'id' => $modalId,
  'role' => 'dialog',
  'tabindex' => '-1',
]) . "\n" ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= Html::tag(
        'div',
        implode('', [
          Html::button(
            Html::tag('span', Icon::close(), ['aria-hidden' => 'true']),
            [
              'class' => 'close',
              'data' => [
                'dismiss' => 'modal',
                'aria-label' => Yii::t('app', 'Close'),
              ],
            ],
          ),
          Html::tag(
            'h4',
            implode(' ', [
              Icon::goldenEgg(),
              Html::encode(Yii::t('app-salmon-overfishing', 'Overfishing Stats')),
            ]),
            ['class' => 'modal-title'],
          ),
        ]),
        ['class' => 'modal-header'],
      ) . "\n" ?>
      <div class="modal-body p-0">
        <table class="table table-bordered table-striped m-0">
          <thead>
            <tr>
              <th class="text-center"><?= Html::encode(Yii::t('app-salmon-overfishing', 'Category')) ?></th>
              <th class="text-center"><?= Html::encode(Yii::t('app-salmon-tide2', 'Water Level')) ?></th>
              <th class="text-center"><?= Html::encode(Yii::t('app-salmon-overfishing', 'Record')) ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">
                <?= Html::encode(Yii::t('app-salmon-overfishing', 'Total Golden Eggs')) . "\n" ?>
              </td>
              <td class="text-center text-muted"><?= Html::encode(Yii::t('app', 'N/A')) ?></td>
              <td class="text-center">
                <?= Icon::goldenEgg() . "\n" ?>
                <?= Html::encode(
                  $fmt->asInteger(
                    TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total.total_3_night')),
                  ),
                ) . "\n" ?>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                <?= Html::encode(Yii::t('app-salmon-overfishing', 'Total Golden Eggs')) ?><br>
                <?= Html::encode(Yii::t('app-salmon-overfishing', '(~2 Night)')) . "\n" ?>
              </td>
              <td class="text-center text-muted"><?= Html::encode(Yii::t('app', 'N/A')) ?></td>
              <td class="text-center">
                <?= Icon::goldenEgg() . "\n" ?>
                <?= Html::encode(
                  $fmt->asInteger(
                    TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total.total_2_night')),
                  ),
                ) . "\n" ?>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                <?= Html::encode(Yii::t('app-salmon-overfishing', 'Total Golden Eggs')) ?><br>
                <?= Html::encode(Yii::t('app-salmon-overfishing', '(~1 Night)')) . "\n" ?>
              </td>
              <td class="text-center text-muted"><?= Html::encode(Yii::t('app', 'N/A')) ?></td>
              <td class="text-center">
                <?= Icon::goldenEgg() . "\n" ?>
                <?= Html::encode(
                  $fmt->asInteger(
                    TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total.total_1_night')),
                  ),
                ) . "\n" ?>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                <?= Html::encode(Yii::t('app-salmon-overfishing', 'Total Golden Eggs')) ?><br>
                <?= Html::encode(Yii::t('app-salmon-overfishing', '(All Normal Waves)')) . "\n" ?>
              </td>
              <td class="text-center text-muted"><?= Html::encode(Yii::t('app', 'N/A')) ?></td>
              <td class="text-center">
                <?= Icon::goldenEgg() . "\n" ?>
                <?= Html::encode(
                  $fmt->asInteger(
                    TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total.total_0_night')),
                  ),
                ) . "\n" ?>
              </td>
            </tr>
            <?= $this->render('./overfishing/event', [
              'event' => null,
              'fmt' => $fmt,
              'stats' => array_values(
                array_filter(
                  ArrayHelper::getValue($stats, 'waves', []),
                  fn ($wave) => $wave['event_id'] === -1,
                ),
              ),
              'tides' => $tides,
            ]) . "\n" ?>
<?php foreach ($events as $event) { ?>
            <?= $this->render('./overfishing/event', [
              'event' => $event,
              'fmt' => $fmt,
              'stats' => array_values(
                array_filter(
                  ArrayHelper::getValue($stats, 'waves', []),
                  fn ($wave) => $wave['event_id'] === $event->id,
                ),
              ),
              'tides' => $tides,
            ]) . "\n" ?>
<?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <?= Html::button(
          implode(' ', [
            Icon::close(),
            Html::encode(Yii::t('app', 'Close')),
          ]),
          [
            'type' => 'button',
            'class' => 'btn btn-primary',
            'data-dismiss' => 'modal',
          ],
        ) . "\n" ?>
      </div>
    </div>
  </div>
</div>
