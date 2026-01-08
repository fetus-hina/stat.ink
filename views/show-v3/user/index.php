<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\data\BaseDataProvider;
use yii\web\View;

/**
 * @var BaseDataProvider $battleDataProvider
 * @var User $user
 * @var View $this
 * @var array $summary
 */

echo implode('', [
  $this->render('pager', ['battleDataProvider' => $battleDataProvider]),
  $this->render('//includes/battles-summary', [
    'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
    'summary' => $summary,
  ]),
  $this->render('buttons', ['user' => $user]),
  $this->render('list', ['battleDataProvider' => $battleDataProvider]),
  $this->render('pager', ['battleDataProvider' => $battleDataProvider]),
]) . "\n";
