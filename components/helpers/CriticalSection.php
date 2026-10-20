<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\helpers;

use Override;
use RuntimeException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\mutex\Mutex;

use function vsprintf;

final class CriticalSection extends Component
{
    public ?string $name = null;
    public int $timeout = 0;
    public Mutex $mutex;

    public static function lock(
        string $name,
        int $timeout = 0,
        ?Mutex $mutex = null,
    ): Resource {
        $config = [
            'class' => static::class,
            'name' => $name,
            'timeout' => $timeout,
        ];
        if ($mutex !== null) {
            $config['mutex'] = $mutex;
        }

        return Yii::createObject($config)->enter();
    }

    #[Override]
    public function init(): void
    {
        parent::init();

        if (!isset($this->mutex)) {
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
