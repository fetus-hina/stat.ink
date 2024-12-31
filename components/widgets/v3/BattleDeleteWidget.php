<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\components\widgets\v3\battleDelete\Button;
use app\components\widgets\v3\battleDelete\Modal;
use app\models\Battle3;
use app\models\Salmon3;
use yii\base\Widget;

use function implode;
use function sprintf;

final class BattleDeleteWidget extends Widget
{
    public Battle3|Salmon3|null $model = null;

    public function run(): string
    {
        if (!$this->isEditable()) {
            return '';
        }

        $modalId = sprintf('%s-modal', (string)$this->id);
        return implode('', [
            Button::widget(['modalSelector' => "#{$modalId}"]),
            Modal::widget(['id' => $modalId, 'model' => $this->model]),
        ]);
    }

    private function isEditable(): bool
    {
        $model = $this->model;
        $user = Yii::$app->user->identity;

        return $model && $user && (int)$model->user_id === (int)$user->id;
    }
}
