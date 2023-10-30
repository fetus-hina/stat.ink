<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use RuntimeException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\mutex\Mutex;

use function vsprintf;

final class CriticalSection extends Component
{
    public $name;
    public $timeout = 0;
    public $mutex;

    public static function lock(
        string $name,
        int $timeout = 0,
        ?Mutex $mutex = null,
    ): Resource {
        return Yii::createObject([
            'class' => static::class,
            'mutex' => $mutex,
            'name' => $name,
            'timeout' => $timeout,
        ])->enter();
    }

    public function init()
    {
        parent::init();

        if ($this->mutex === null) {
            $this->mutex = Yii::$app->mutex;
        }
    }

    public function enter(): Resource
    {
        if (!$this->name) {
            throw new InvalidConfigException('$mutex->name does not specified.');
        }

        if ($this->timeout < 0) {
            throw new InvalidConfigException('$mutex->timeout is now negative value.');
        }

        if (!$this->mutex instanceof Mutex) {
            throw new InvalidConfigException('$mutex->mutex is not instance of ' . Mutex::class . '.');
        }

        Yii::trace(__METHOD__ . '(): Entering a critical section that named ' . $this->name);
        Yii::beginProfile(__METHOD__ . ', acquire');
        $status = $this->mutex->acquire($this->name, $this->timeout);
        Yii::endProfile(__METHOD__ . ', acquire');
        if (!$status) {
            Yii::warning(
                vsprintf('%s(): Resource is busy, could not enter to a critical section that named %s', [
                    __METHOD__,
                    $this->name,
                ]),
            );

            throw new RuntimeException('Resource is busy.');
        }

        Yii::trace(__METHOD__ . '(): Entered to a critical section that named ' . $this->name);
        return new Resource($this->name, function ($name) {
            $this->mutex->release($name);
            Yii::trace(__METHOD__ . '(): Leave from a critical section that named ' . $name);
        });
    }
}
