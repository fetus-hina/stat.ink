<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use app\components\widgets\BattleSummaryDialog;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotSymbolAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

use function vsprintf;

class BattleSummaryDialogAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'battle-summary-dialog.js',
    ];
    public $depends = [
        AppAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        FlotAsset::class,
        FlotSymbolAsset::class,
        JqueryAsset::class,
    ];

    public function init()
    {
        parent::init();

        Yii::$app->view->on(View::EVENT_END_BODY, function ($event): void {
            $dialog = BattleSummaryDialog::begin();
            BattleSummaryDialog::end();

            Yii::$app->view->registerJs(vsprintf('jQuery(%s).battleSummaryDialog(%s)', [
                Json::encode('#' . $dialog->id),
                Json::encode('.summary-box-plot[data-stats]'),
            ]));
        });
    }
}
