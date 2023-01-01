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

use function implode;
use function mb_chr;

final class Icon
{
    // android
    // apiJson
    // back
    // blog
    // caretDown
    // checkboxChecked
    // checkboxEmpty
    // close
    // colorScheme
    // config
    // delete
    // download
    // dummy
    // feed
    // fileCsv
    // fileJson
    // github
    // help
    // image
    // ios
    // language
    // languageLevelFew
    // languageLevelMachine
    // languageLevelPartical
    // link
    // linux
    // login
    // loginHistory
    // logout
    // macOs
    // nextPage
    // ok
    // permalink
    // prevPage
    // refresh
    // scrollTo
    // search
    // stats
    // subPage
    // timezone
    // twitter
    // unknown
    // user
    // userAdd
    // videoLink
    // windows

    public static function android(): string
    {
        return self::bi('android2');
    }

    public static function apiJson(): string
    {
        return self::bi('braces');
    }

    public static function back(): string
    {
        return self::bi('chevron-left');
    }

    public static function blog(): string
    {
        return self::bi('wordpress');
    }

    public static function caretDown(): string
    {
        return self::bi('caret-down-fill');
    }

    public static function checkboxChecked(): string
    {
        return self::bi('check2-square');
    }

    public static function checkboxEmpty(): string
    {
        return self::bi('square');
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

    public static function delete(): string
    {
        return self::bi('trash3');
    }

    public static function download(): string
    {
        return self::bi('download');
    }

    public static function dummy(): string
    {
        $view = Yii::$app->view;
        if ($view instanceof View) {
            return Html::tag('span', ' ', [
                'class' => 'd-inline-block',
                'style' => ['width' => '1em'],
            ]);
        }

        return mb_chr(0x3000, 'UTF-8'); // Ideographic Space
    }

    public static function feed(): string
    {
        return self::bi('rss');
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

    public static function image(): string
    {
        return self::bi('image');
    }

    public static function ios(): string
    {
        return self::macOs();
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

    public static function linux(): string
    {
        return self::bi('ubuntu'); // FIXME
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

    public static function macOs(): string
    {
        return self::bi('apple');
    }

    public static function nextPage(): string
    {
        return self::bi('chevron-double-right');
    }

    public static function ok(): string
    {
        return self::bi('check-lg');
    }

    public static function permalink(): string
    {
        return self::link();
    }

    public static function prevPage(): string
    {
        return self::bi('chevron-double-left');
    }

    public static function refresh(): string
    {
        return self::bi('arrow-repeat');
    }

    public static function scrollTo(): string
    {
        return self::bi('chevron-down');
    }

    public static function search(): string
    {
        return self::bi('search');
    }

    public static function stats(): string
    {
        return self::bi('pie-chart-fill');
    }

    public static function subCategory(): string
    {
        return self::bi('chevron-double-right');
    }

    public static function subPage(): string
    {
        return self::bi('chevron-right');
    }

    public static function timezone(): string
    {
        return self::bi('clock');
    }

    public static function twitter(): string
    {
        return self::bi(
            'twitter',
            css: implode('', [
                '.bi-twitter{color:#1da1f2}',
                '.btn .bi-twitter{color:inherit}',
            ]),
        );
    }

    public static function unknown(): string
    {
        return self::bi('question');
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

    public static function videoLink(): string
    {
        return self::bi('play-fill');
    }

    public static function windows(): string
    {
        return self::bi('windows');
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
