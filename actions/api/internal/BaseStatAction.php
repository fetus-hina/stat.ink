<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\api\internal;

use Override;
use Yii;
use app\models\User;
use yii\web\ViewAction as BaseAction;

use function hash_hmac;
use function http_build_query;
use function is_scalar;

abstract class BaseStatAction extends BaseAction
{
    public ?User $user = null;

    public function getCacheFormatVersion(): int
    {
        return 1;
    }

    public function getCacheId(): string
    {
        return hash_hmac(
            'sha256',
            http_build_query(
                ['user_id' => $this->user->id, '__format' => $this->getCacheFormatVersion()],
                '',
                '&',
            ),
            static::class,
        );
    }

    public function getCacheExpires(): int
    {
        return 600;
    }

    #[Override]
    public function run(): mixed
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        $request = Yii::$app->getRequest();
        $screenName = $request->get('screen_name');

        if (!is_scalar($screenName)) {
            return ['error' => ['screen_name' => ['not found']]];
        }

        if (!$this->user = User::findOne(['screen_name' => $screenName])) {
            return ['error' => ['screen_name' => ['not found']]];
        }

        return $this->decorate($this->makeDataOrLoadCache());
    }

    private function makeDataOrLoadCache(): mixed
    {
        $cache = Yii::$app->cache;
        $key = $this->getCacheId();
        $data = $cache->get($key);
        if ($data !== false) {
            return $data;
        }
        $data = $this->makeData();
        $cache->set($key, $data, $this->getCacheExpires());
        return $data;
    }

    abstract protected function makeData(): mixed;

    protected function decorate(mixed $data): mixed
    {
        return $data;
    }
}
