<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\base\InvalidConfigException;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;

use function in_array;

class BootswatchAsset extends AssetBundle
{
    public $sourcePath = '@node/bootswatch';
    public $css = [];
    public $depends = [
        BootstrapPluginAsset::class,
    ];
    private $theme;

    public function init()
    {
        parent::init();

        if (!$this->theme) {
            $this->setTheme('cerulean');
        }
    }

    public function setTheme(string $theme): self
    {
        if (!static::isValidTheme($theme)) {
            throw new InvalidConfigException("Invalid theme: {$theme}");
        }

        $this->theme = $theme;
        $this->css = [
            "{$theme}/bootstrap.min.css",
        ];

        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public static function isValidTheme(string $theme): bool
    {
        $list = [
            'cerulean',
            'cosmo',
            'cyborg',
            'darkly',
            'flatly',
            'journal',
            'lumen',
            'paper',
            'readable',
            'sandstone',
            'simplex',
            'slate',
            'spacelab',
            'superhero',
            'united',
            'yeti',
        ];

        return !!in_array($theme, $list, true);
    }
}
