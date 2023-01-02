<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;

use function trim;

abstract class BaseUAIconWidget extends Widget
{
    public $ua;
    public $size = '1em';

    public function run()
    {
        $ua = trim((string)$this->ua);
        if ($ua === '') {
            return '';
        }

        $id = $this->id;
        $this->registerTrigger($id, [
            'ua' => $ua,
            'size' => $this->size,
        ]);
        return Html::tag('span', '', [
            'id' => $this->id,
        ]);
    }

    abstract protected function registerTrigger(string $id, array $options): void;
}
