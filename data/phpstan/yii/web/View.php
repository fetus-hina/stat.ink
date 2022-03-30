<?php

declare(strict_types=1);

namespace yii\web;

class Controller
{
}

class View
{
    /** @var Controller */
    public $context;

    /**
     * @param mixed $value
     */
    public function registerJsVar(string $name, $value, int $position = self::POS_HEAD): void
    {
    }
}
