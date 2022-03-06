<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace app\components\web;

use Base32\Base32;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\AssetManager as FWAssetManager;

use function call_user_func;
use function dirname;
use function gmdate;
use function hash_hmac;
use function is_callable;
use function is_file;
use function is_int;
use function ltrim;
use function strlen;
use function strncmp;
use function strtolower;
use function substr;
use function vsprintf;

class AssetManager extends FWAssetManager
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
            'assetRevision' => (int)ArrayHelper::getValue(Yii::$app->params, 'assetRevision', -1),
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
        $hash = strtolower(substr(
            Base32::encode(hash_hmac('sha256', $path, Json::encode($options), true)),
            0,
            16
        ));
        Yii::endProfile($profile, __METHOD__);
        Yii::info("Asset path hash = {$hash}", __METHOD__);

        /** @var ?int $commitTime */
        $commitTime = ArrayHelper::getValue(Yii::$app->params, 'gitRevision.lastCommittedT');
        if (!is_int($commitTime)) {
            Yii::info('Commit time is unknown. No timestamp used', __METHOD__);
            return $hash;
        }

        $result = vsprintf('%s-%s/%s', [
            gmdate('Ymd', $commitTime),
            $options['assetRevision'] >= 0
                ? (string)$options['assetRevision']
                : gmdate('His', $commitTime),
            $hash,
        ]);
        Yii::info("Asset path = {$result}", __METHOD__);
        return $result;
    }
}
