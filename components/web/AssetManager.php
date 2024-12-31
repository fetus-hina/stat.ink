<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\web;

use ParagonIE\ConstantTime\Base32;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\AssetManager as FWAssetManager;

use function call_user_func;
use function dirname;
use function hash_hmac;
use function is_callable;
use function is_file;
use function ltrim;
use function str_replace;
use function strlen;
use function strncmp;
use function substr;
use function vsprintf;

final class AssetManager extends FWAssetManager
{
    /**
     * @param string $path
     * @return string
     */
    protected function hash($path)
    {
        if (is_callable($this->hashCallback)) {
            return call_user_func($this->hashCallback, $path);
        }

        $options = [
            'assetRevision' => (string)ArrayHelper::getValue(Yii::$app->params, 'assetRevision', -1),
            'linkAssets' => !!$this->linkAssets,
        ];
        Yii::info("Asset revision = {$options['assetRevision']}", __METHOD__);

        $appPath = dirname(Yii::getAlias('@webroot'));
        $path = (is_file($path) ? dirname($path) : $path);
        if (strncmp($path, $appPath, strlen($appPath)) === 0) {
            $path = '@app/' . ltrim(substr($path, strlen($appPath)), '/');
        }

        Yii::info("Asset path = {$path}", __METHOD__);
        $profile = "Calc hash ({$path})";
        Yii::beginProfile($profile, __METHOD__);
        $hash = substr(
            Base32::encodeUnpadded(
                hash_hmac('sha256', $path, Json::encode($options), true),
            ),
            0,
            8,
        );
        Yii::endProfile($profile, __METHOD__);
        Yii::info("Asset path hash = {$hash}", __METHOD__);

        $result = str_replace(
            '//',
            '/',
            vsprintf('%s/%s', [
                $options['assetRevision'],
                $hash,
            ]),
        );
        Yii::info("Asset path = {$result}", __METHOD__);
        return $result;
    }
}
