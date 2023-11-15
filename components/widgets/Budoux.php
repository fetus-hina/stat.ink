<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\budoux\BudouxWebComponentsJaAsset;
use app\assets\budoux\BudouxWebComponentsZhHansAsset;
use app\assets\budoux\BudouxWebComponentsZhHantAsset;
use app\components\helpers\TypeHelper;
use yii\base\Widget;
use yii\web\AssetBundle;
use yii\web\View;

use function ob_get_clean;
use function ob_start;
use function preg_match;
use function vsprintf;

final class Budoux extends Widget
{
    public ?string $lang = null;

    public function init(): void
    {
        parent::init();
        ob_start();
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function run()
    {
        $html = (string)ob_get_clean();
        $lang = TypeHelper::string($this->lang ?? Yii::$app->language);
        return match (true) {
            (bool)preg_match('/^ja\b/i', $lang) => $this->decorateJa($html),
            (bool)preg_match('/^zh-cn\b/i', $lang) => $this->decorateHans($html),
            (bool)preg_match('/^zh-tw\b/i', $lang) => $this->decorateHant($html),
            default => $html,
        };
    }

    private function decorateJa(string $html): string
    {
        return $this->decorate(
            $html,
            'budoux-ja',
            BudouxWebComponentsJaAsset::class,
        );
    }

    private function decorateHans(string $html): string
    {
        return $this->decorate(
            $html,
            'budoux-zh-hans',
            BudouxWebComponentsZhHansAsset::class,
        );
    }

    private function decorateHant(string $html): string
    {
        return $this->decorate(
            $html,
            'budoux-zh-hant',
            BudouxWebComponentsZhHantAsset::class,
        );
    }

    /**
     * @param class-string<AssetBundle> $asset
     */
    private function decorate(string $html, string $tag, string $asset): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            $view->registerAssetBundle($asset);
        }

        return vsprintf('<%1$s>%2$s</%1$s>', [
            $tag,
            $html,
        ]);
    }
}
