<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use Yii;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotSymbolAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

class BattleSummaryDialogAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'battle-summary-dialog.css',
    ];
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

        Yii::$app->view->on(View::EVENT_END_BODY, function ($event) : void {
            echo Yii::$app->view->render('//includes/_battles-summary-modal') . "\n";
        });
    }
}
