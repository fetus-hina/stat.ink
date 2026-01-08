<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

use function http_build_query;
use function implode;

class IntlPolyfillAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [];

    public function init()
    {
        parent::init();

        $features = [];
        $features[] = 'Intl.~locale.en-US';
        $features[] = 'Intl.~locale.en';
        $features[] = 'Intl.~locale.' . Yii::$app->getLocale();

        $this->js[] = 'https://cdn.polyfill.io/v2/polyfill.min.js?' . http_build_query(
            ['features' => implode(',', $features)],
            '',
            '&',
        );
    }
}
