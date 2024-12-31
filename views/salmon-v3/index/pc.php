<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
