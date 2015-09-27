<?php
namespace app\components\helpers;

class Resource
{
    protected $res;
    protected $free;

    public function __construct($resource, $freeFunc)
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

    public function get()
    {
        return $this->res;
    }
}
