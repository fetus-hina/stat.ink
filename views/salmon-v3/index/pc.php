<?php

declare(strict_types=1);

use app\models\User;
use yii\data\BaseDataProvider;
use yii\web\View;

/**
 * @var BaseDataProvider $dataProvider
 * @var User $user
 * @var View $this
 */

?>
<?= $this->render('pc/pager', compact('dataProvider')) . "\n" ?>
<?php /*
  echo $this->render('_summary', [
    'summary' => $dataProvider->query->summary(),
  ]) . "\n";
*/ ?>
<?= $this->render('pc/buttons', compact('user')) . "\n" ?>
<?= $this->render('pc/list', compact('dataProvider', 'user')) . "\n" ?>
<?= $this->render('pc/pager', compact('dataProvider')) . "\n" ?>
