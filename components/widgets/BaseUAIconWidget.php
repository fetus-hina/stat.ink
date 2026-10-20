<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use app\components\helpers\TypeHelper;
use yii\base\Widget;
use yii\helpers\Html;

use function trim;

abstract class BaseUAIconWidget extends Widget
{
    public ?string $ua = null;
    public string $size = '1em';

    public function run()
    {
        $ua = trim((string)$this->ua);
        if ($ua === '') {
            return '';
        }

        $id = TypeHelper::string($this->getId());
        $this->registerTrigger($id, [
            'ua' => $ua,
            'size' => $this->size,
        ]);
        return Html::tag('span', '', [
            'id' => $this->id,
        ]);
    }

    /**
     * @param array<string, mixed> $options
     */
    abstract protected function registerTrigger(string $id, array $options): void;
}
