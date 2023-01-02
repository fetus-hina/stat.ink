<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\BrowserIconWidgetAsset;
use app\assets\BrowserLogosAsset;
use yii\helpers\Json;

use function array_merge;
use function preg_replace;
use function sprintf;

class BrowserIconWidget extends BaseUAIconWidget
{
    protected function registerTrigger(string $id, array $options): void
    {
        BrowserIconWidgetAsset::register($this->view);
        $logos = BrowserLogosAsset::register($this->view);
        $am = Yii::$app->getAssetManager();
        $this->view->registerJs(sprintf(
            '$("#%s").browserIconWidget(%s)',
            $id,
            Json::encode(array_merge($options, [
                // get base-url and remove timestamp query
                'logos' => preg_replace(
                    '!/XXXXXXXX.*$!',
                    '/',
                    $am->getAssetUrl($logos, 'XXXXXXXX'),
                ),
            ])),
        ));
    }
}
