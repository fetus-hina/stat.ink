<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\web;

use Yii;
use yii\helpers\Url;

/**
 * @inheritdoc
 */
class AssetManager extends \yii\web\AssetManager
{
    /**
     * @inheritdoc
     */
    public function getAssetUrl($bundle, $asset)
    {
        if (($actualAsset = $this->resolveAsset($bundle, $asset)) !== false) {
            if (strncmp($actualAsset, '@web/', 5) === 0) {
                $asset = substr($actualAsset, 5);
                $basePath = Yii::getAlias("@webroot");
                $baseUrl = Yii::getAlias("@web");
            } else {
                $asset = Yii::getAlias($actualAsset);
                $basePath = $this->basePath;
                $baseUrl = $this->baseUrl;
            }
        } else {
            $basePath = $bundle->basePath;
            $baseUrl = $bundle->baseUrl;
        }

        if (!Url::isRelative($asset) || strncmp($asset, '/', 1) === 0) {
            return $asset;
        }

        if ($this->appendTimestamp) {
            foreach (['', '.gz'] as $appendExtension) {
                if (($timestamp = @filemtime("$basePath/{$asset}{$appendExtension}")) > 0) {
                    return "$baseUrl/$asset?v=$timestamp";
                }
            }
        }
        return "$baseUrl/$asset";
    }
}
