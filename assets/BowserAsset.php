<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class BowserAsset extends AssetBundle
{
    public $sourcePath = '@node/bowser';
    public $js = [
        'es5.js',
    ];
    public $depends = [
        BabelPolyfillAsset::class,
    ];

    public function init()
    {
        parent::init();
        $this->publishOptions['filter'] = [$this, 'filterIsUsableAssets'];
    }

    public function filterIsUsableAssets(string $path): bool
    {
        $prefix = Yii::getAlias($this->sourcePath);
        if (substr($path, 0, strlen($prefix)) !== $prefix) {
            return false;
        }

        $path = substr($path, strlen($prefix) + 1); // /path/to/es5.js => es5.js
        if ($path === 'LICENSE') {
            return true;
        }

        return (bool)preg_match('![^/]+\.js$!i', $path);
    }
}
