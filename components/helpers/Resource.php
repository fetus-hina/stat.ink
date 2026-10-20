<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\helpers;

class Resource
{
    protected mixed $res = null;
    /**
     * @var callable|null
     */
    protected $free;

    public function __construct(mixed $resource, callable $freeFunc)
    {
        $this->res = $resource;
        $this->free = $freeFunc;
    }

    public function __destruct()
    {
        if ($this->free && $this->res) {
            $f = $this->free;
            $f($this->res);
        }
    }

    public function get(): mixed
    {
        return $this->res;
    }
}
