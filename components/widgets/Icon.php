<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\View;

final class Icon
{
    // apiJson
    // close
    // colorScheme
    // config
    // fileCsv
    // fileJson
    // github
    // help
    // language
    // languageLevelFew
    // languageLevelMachine
    // languageLevelPartical
    // link
    // login
    // loginHistory
    // logout
    // refresh
    // timezone
    // twitter
    // user
    // userAdd

    public static function apiJson(): string
    {
        return self::bi('braces');
    }

    public static function close(): string
    {
        return self::bi('x-lg');
    }

    public static function colorScheme(): string
    {
        return self::bi('palette-fill');
    }

    public static function config(): string
    {
        return self::bi('person-fill-gear');
    }

    public static function fileCsv(): string
    {
        return self::bi('filetype-csv');
    }

    public static function fileJson(): string
    {
        return self::bi('filetype-json');
    }

    public static function github(): string
    {
        return self::bi('github');
    }

    public static function help(): string
    {
        return self::bi('question-circle-fill');
    }

    public static function language(): string
    {
        return self::bi('translate');
    }

    public static function languageLevelFew(): string
    {
        return self::bi('exclamation-triangle-fill');
    }

    public static function languageLevelMachine(): string
    {
        return self::bi('robot');
    }

    public static function languageLevelPartical(): string
    {
        return self::bi('info-circle-fill');
    }

    public static function link(): string
    {
        return self::bi('link-45deg');
    }

    public static function login(): string
    {
        return self::bi('box-arrow-in-right');
    }

    public static function loginHistory(): string
    {
        return self::bi('clock-history');
    }

    public static function logout(): string
    {
        return self::bi('box-arrow-right');
    }

    public static function refresh(): string
    {
        return self::bi('arrow-repeat');
    }

    public static function timezone(): string
    {
        return self::bi('clock');
    }

    public static function twitter(): string
    {
        return self::bi(
            'twitter',
            css: \implode('', [
                '.bi-twitter{color:#1da1f2}',
                '.btn .bi-twitter{color:inherit}',
            ]),
        );
    }

    public static function user(): string
    {
        return self::bi('person-fill');
    }

    public static function userAdd(): string
    {
        return self::bi('person-plus-fill');
    }

    public static function users(): string
    {
        return self::bi('people-fill');
    }

    private static function bi(string $name, ?string $css = null): string
    {
        self::prepareAsset(BootstrapIconsAsset::class, css: $css);

        return Html::tag('span', '', [
            'aria' => ['hidden' => 'true'],
            'class' => ['bi', "bi-{$name}"],
        ]);
    }

    /**
     * @phpstan-param class-string<AssetBundle> $fqcn
     */
    private static function prepareAsset(string $fqcn, ?string $css = null): void
    {
        $view = Yii::$app->view;
        if ($view && $view instanceof View) {
            $view->registerAssetBundle($fqcn);

            if ($css !== null) {
                $view->registerCss($css);
            }
        }
    }
}
