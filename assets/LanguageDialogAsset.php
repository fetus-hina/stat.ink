<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class LanguageDialogAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    /**
     * @var list<string>
     */
    public $js = [
        'language-dialog.js',
    ];

    /**
     * @var list<string>
     */
    public $css = [
        'language-dialog.css',
    ];

    /**
     * @var list<class-string<AssetBundle>>
     */
    public $depends = [
        ApiFetchAsset::class,
        BootstrapAsset::class,
        JqueryAsset::class,
    ];
}
