<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\OsIconWidgetAsset;
use app\assets\OsLogosAsset;
use yii\helpers\Json;

use function array_merge;
use function preg_replace;
use function sprintf;

class OsIconWidget extends BaseUAIconWidget
{
    protected function registerTrigger(string $id, array $options): void
    {
        OsIconWidgetAsset::register($this->view);
        $logos = OsLogosAsset::register($this->view);
        $am = Yii::$app->getAssetManager();
        $this->view->registerJs(sprintf(
            '$("#%s").osIconWidget(%s)',
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
