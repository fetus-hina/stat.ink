<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo $this->render('exports/splatoon3', compact('user')) . "\n";
echo $this->render('exports/splatoon2', compact('user')) . "\n";
echo $this->render('exports/splatoon1', compact('user')) . "\n";
