<?php
use app\components\widgets\AdWidget;
use app\components\widgets\JdenticonWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Update Your Icon'),
]);
?>
<div class="container">
  <h1>
    <?= Html::encode(Yii::t('app', 'Update Your Icon')) . "\n" ?>
  </h1>
  <p>
    <?= Html::encode(Yii::t('app', 'Your current icon:')) . "\n" ?>
    <span class="profile-icon">
<?php if ($user->userIcon) { ?>
      <?= Html::img($user->userIcon->url, [
        'width' => 48,
        'height' => 48,
      ]) . "\n" ?>
<?php } else { ?>
      <?= JdenticonWidget::widget([
        'hash' => $user->identiconHash,
        'class' => 'identicon',
        'size' => 48,
      ]) . "\n" ?>
<?php } ?>
    </span>
  </p>
  <div class="row">
    <div class="col-xs-12 col-sm-6" style="padding:0 5%">
      <div class="form-group">
        <?= Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'fa fa-angle-double-left fa-fw']),
            Html::encode(Yii::t('app', 'Back')),
          ]),
          ['user/profile'],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </div>
<?php if (Yii::$app->params['twitter']['read_enabled'] ?? null) { ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <?= Html::encode(Yii::t('app', 'Use profile icon of your twitter account')) . "\n" ?>
        </div>
        <div class="panel-body">
          <p class="text-right">
            <?= Html::a(
              implode('', [
                Html::tag('span', '', ['class' => 'fab fa-fw fa-twitter']),
                Html::encode(Yii::t('app', 'Use your profile icon')),
              ]),
              ['icon-twitter'],
              ['class' => 'btn btn-info btn-block']
            ) . "\n" ?>
          </p>
        </div>
      </div>
<?php } ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <?= Html::encode(Yii::t('app', 'Upload new image')) . "\n" ?>
        </div>
        <div class="panel-body">
          <?= Html::beginForm(['edit-icon'], 'post', ['enctype' => 'multipart/form-data']) . "\n" ?>
            <input type="hidden" name="action" value="update">
            <ul>
              <li>
                <?= Html::encode(
                  Yii::t('app', 'PNG/JPEG file up to {0}', ['1 MiB'])
                ) . "\n" ?>
              </li>
              <li>
                <?= Html::encode(
                  Yii::t('app', '{0}×{1} or less resolution', [1000, 1000])
                ) . "\n" ?>
              </li>
            </ul>
            <div class="form-group">
              <input type="file" name="image" value="" class="" required>
            </div>
            <?= Html::submitButton(
              implode('', [
                Html::tag('span', '', ['class' => 'fa fa-fw fa-upload']),
                Html::encode(Yii::t('app', 'Upload icon')),
              ]),
              ['class' => 'btn btn-info btn-block']
            ) . "\n" ?>
          <?= Html::endForm() . "\n" ?>
        </div>
      </div>
<?php if ($current) { ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <?= Html::encode(Yii::t('app', 'Reset to default icon')) . "\n" ?>
        </div>
        <div class="panel-body">
          <?= Html::beginForm(['edit-icon'], 'post') . "\n" ?>
            <input type="hidden" name="action" value="delete">
            <p>
              <?= Html::encode(
                Yii::t('app', 'Your current image will be deleted and reset to auto-generated image.')
              ) . "\n" ?>
            </p>
            <p>
              <?= Html::encode(Yii::t('app', 'The icon will be:')) . "\n" ?>
              <?= JdenticonWidget::widget([
                'hash' => $user->identiconHash,
                'class' => 'identicon',
                'size' => 48,
              ]) . "\n" ?>
            </p>
            <p>
              <?= Html::submitButton(
                implode('', [
                  Html::tag('span', '', ['class' => 'fa fa-fw fa-undo']),
                  Html::encode(Yii::t('app', 'Reset icon')),
                ]),
                ['class' => 'btn btn-danger btn-block']
              ) . "\n" ?>
            </p>
          <?= Html::endForm() . "\n" ?>
        </div>
      </div>
<?php } ?>
      <div class="form-group">
        <?= Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-left']),
            Html::encode(Yii::t('app', 'Back')),
          ]),
          ['/user/profile'],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
