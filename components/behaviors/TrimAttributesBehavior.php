<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Traversable;
use yii\base\Behavior;
use yii\base\Model;

use function is_array;
use function is_bool;
use function is_scalar;
use function trim;

class TrimAttributesBehavior extends Behavior
{
    /**
     * @var array<array-key, mixed>
     */
    public $targets;

    /**
     * @var bool
     */
    public $recursive;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        if (!is_array($this->targets)) {
            $this->targets = [];
        }

        if ($this->recursive !== false) {
            $this->recursive = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => [$this, 'trim'],
        ];
    }

    public function trim(): void
    {
        foreach ($this->targets as $attrName) {
            $this->owner->{$attrName} = $this->doTrim($this->owner->{$attrName});
        }
    }

    protected function doTrim($value)
    {
        if ($value === null || is_bool($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            $value = trim((string)$value);
            return $value === '' ? null : $value;
        }

        if (is_array($value) || $value instanceof Traversable) {
            if ($this->recursive) {
                foreach ($value as $k => $v) {
                    $value[$k] = $this->doTrim($v);
                }
            }

            return $value;
        }

        return $value;
    }
}
