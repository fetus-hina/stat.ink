<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;

use function hash;
use function preg_match;
use function sprintf;

class NumberFormatAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [];
    public $css = [];

    public function publish($am)
    {
        parent::publish($am);

        $formatted = Yii::$app->formatter->asDecimal(1000.5, 1);
        if (preg_match('/^1(.)000(.)5$/', $formatted, $match)) {
            $seps = [
                'decimal' => $match[2],
                'thousand' => $match[1],
            ];
        } else {
            $seps = [
                'decimal' => Yii::$app->formatter->decimalSeparator ?: '.',
                'thousand' => Yii::$app->formatter->thousandSeparator ?: ',',
            ];
        }

        Yii::$app->view->registerJs(
            sprintf('window.numberFormat = %s;', Json::encode($seps)),
            View::POS_HEAD,
            hash('md5', self::class),
        );
    }
}
