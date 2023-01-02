<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo $this->render('exports/splatoon2', compact('user')) . "\n";
echo $this->render('exports/splatoon1', compact('user')) . "\n";
