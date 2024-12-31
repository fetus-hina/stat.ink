<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;

final class ApiInfoName extends Widget
{
    public ?string $enName = null;
    public ?string $name = null;
    public ?string $lang = null;

    public function run(): ?string
    {
        $enName = $this->enName;
        $name = $this->name;
        $lang = $this->lang;
        if ($enName === null || $name === null || $lang === null) {
            return null;
        }

        return Html::tag(
            $lang === 'en-US' || $name !== $enName ? null : 'span',
            Html::encode($name),
            ['class' => 'text-muted'],
        );
    }
}
