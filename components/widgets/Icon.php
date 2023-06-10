<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\BootstrapIconsAsset;
use app\assets\MedalAsset;
use app\assets\SalmonEggAsset;
use app\components\helpers\TypeHelper;
use yii\base\UnknownMethodException;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use yii\web\View;

use function implode;
use function mb_chr;

/**
 * @method static string addSomething()
 * @method static string android()
 * @method static string apiJson()
 * @method static string appLink()
 * @method static string appUnlink()
 * @method static string arrowRight()
 * @method static string back()
 * @method static string blog()
 * @method static string bronzeMedal()
 * @method static string bronzeScale()
 * @method static string caretDown()
 * @method static string check()
 * @method static string checkboxChecked()
 * @method static string checkboxEmpty()
 * @method static string close()
 * @method static string colorScheme()
 * @method static string config()
 * @method static string crown()
 * @method static string delete()
 * @method static string discord()
 * @method static string download()
 * @method static string dummy()
 * @method static string edit()
 * @method static string feed()
 * @method static string fileCsv()
 * @method static string fileJson()
 * @method static string filter()
 * @method static string github()
 * @method static string goldMedal()
 * @method static string goldScale()
 * @method static string goldenEgg()
 * @method static string hasDisconnected()
 * @method static string help()
 * @method static string image()
 * @method static string info()
 * @method static string ios()
 * @method static string language()
 * @method static string languageLevelFew()
 * @method static string languageLevelMachine()
 * @method static string languageLevelPartical()
 * @method static string link()
 * @method static string linux()
 * @method static string list()
 * @method static string listConfig()
 * @method static string login()
 * @method static string loginHistory()
 * @method static string logout()
 * @method static string lowerBound()
 * @method static string macOs()
 * @method static string nextPage()
 * @method static string no()
 * @method static string number();
 * @method static string ok()
 * @method static string permalink()
 * @method static string popup()
 * @method static string powerEgg()
 * @method static string prevPage()
 * @method static string refresh()
 * @method static string scrollTo()
 * @method static string search()
 * @method static string silverMedal()
 * @method static string silverScale()
 * @method static string slack()
 * @method static string sortable()
 * @method static string stats()
 * @method static string subCategory()
 * @method static string subPage()
 * @method static string thisPlayer()
 * @method static string timezone()
 * @method static string twitter()
 * @method static string unknown()
 * @method static string upperBound()
 * @method static string user()
 * @method static string userAdd()
 * @method static string users()
 * @method static string videoLink()
 * @method static string windows()
 * @method static string yes()
 */
final class Icon
{
    /**
     * @var array<string, string>
     */
    private static $biMap = [
        'addSomething' => 'plus-circle',
        'android' => 'android2',
        'apiJson' => 'braces',
        'arrowRight' => 'arrow-right',
        'back' => 'chevron-left',
        'blog' => 'wordpress',
        'caretDown' => 'caret-down-fill',
        'check' => 'check-lg',
        'checkboxChecked' => 'check2-square',
        'checkboxEmpty' => 'square',
        'close' => 'x-lg',
        'colorScheme' => 'palette-fill',
        'config' => 'person-fill-gear',
        'delete' => 'trash3',
        'discord' => 'discord',
        'download' => 'download',
        'edit' => 'pencil-square',
        'feed' => 'rss',
        'fileCsv' => 'filetype-csv',
        'fileJson' => 'filetype-json',
        'filter' => 'funnel-fill',
        'github' => 'github',
        'help' => 'question-circle-fill',
        'image' => 'image',
        'info' => 'info-circle-fill',
        'ios' => 'apple',
        'language' => 'translate',
        'languageLevelFew' => 'exclamation-triangle-fill',
        'languageLevelMachine' => 'robot',
        'languageLevelPartical' => 'info-circle-fill',
        'link' => 'link-45deg',
        'linux' => 'ubuntu', // mmm...
        'list' => 'list',
        'listConfig' => 'gear',
        'login' => 'box-arrow-in-right',
        'loginHistory' => 'clock-history',
        'logout' => 'box-arrow-right',
        'lowerBound' => 'arrow-down-short',
        'macOs' => 'apple',
        'nextPage' => 'chevron-double-right',
        'no' => 'x-lg',
        'number' => 'hash',
        'ok' => 'check-lg',
        'permalink' => 'link-45deg',
        'popup' => 'window-stack',
        'prevPage' => 'chevron-double-left',
        'refresh' => 'arrow-repeat',
        'scrollTo' => 'chevron-down',
        'search' => 'search',
        'slack' => 'slack',
        'sortable' => 'arrow-down-up',
        'stats' => 'pie-chart-fill',
        'subCategory' => 'chevron-double-right',
        'subPage' => 'chevron-right',
        'timezone' => 'clock',
        'unknown' => 'question',
        'upperBound' => 'arrow-up-short',
        'user' => 'person-fill',
        'userAdd' => 'person-plus-fill',
        'users' => 'people-fill',
        'videoLink' => 'play-fill',
        'windows' => 'windows',
        'yes' => 'check-lg',
    ];

    /**
     * @var array<string, string>
     */
    private static $fasMap = [
        'appLink' => 'link',
        'appUnlink' => 'unlink',
        'crown' => 'crown',
        'hasDisconnected' => 'tint-slash',
    ];

    /**
     * @var array<string, array{class-string<AssetBundle>, string}>
     */
    private static $assetImageMap = [
        'goldenEgg' => [SalmonEggAsset::class, 'golden-egg.png'],
        'powerEgg' => [SalmonEggAsset::class, 'power-egg.png'],
    ];

    public static function bronzeMedal(): string
    {
        return self::medalHtml('bronze');
    }

    public static function bronzeScale(): string
    {
        return self::medalHtml('bronze');
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

    public static function goldMedal(): string
    {
        return self::medalHtml('gold');
    }

    public static function goldScale(): string
    {
        return self::medalHtml('gold');
    }

    public static function silverMedal(): string
    {
        return self::medalHtml('silver');
    }

    public static function silverScale(): string
    {
        return self::medalHtml('silver');
    }

    public static function thisPlayer(): string
    {
        return self::fas(
            'level-up-alt',
            modifier: fn (FA $v): FA => $v->fw()->rotate(90),
        );
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

    public static function __callStatic(string $name, $args): string
    {
        return match (true) {
            isset(self::$biMap[$name]) => self::bi(self::$biMap[$name]),
            isset(self::$fasMap[$name]) => self::fas(self::$fasMap[$name]),
            isset(self::$assetImageMap[$name]) => self::assetImage(...self::$assetImageMap[$name]),
            default => throw new UnknownMethodException("Unknown icon {$name}"),
        };
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
     * @param (callable(FA): FA)|null $modifier
     */
    private static function fas(string $name, ?callable $modifier = null): string
    {
        $o = FA::fas($name);
        return (string)($modifier ? $modifier($o) : $o);
    }

    private static function medalHtml(string $color): string
    {
        self::prepareAsset(MedalAsset::class);

        return Html::tag('span', '', [
            'class' => [
                'medal-icon',
                'medal-icon-' . $color,
            ],
        ]);
    }

    /**
     * @param class-string<AssetBundle> $assetClass
     */
    private static function assetImage(string $assetClass, string $assetPath): string
    {
        // self::prepareAsset($assetClass);
        $am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);

        return Html::img($am->getAssetUrl($am->getBundle($assetClass), $assetPath), [
            'class' => 'basic-icon',
            'draggable' => 'false',
            'style' => [
                '--icon-height' => '1em',
                '--icon-valign' => 'middle',
            ],
        ]);
    }

    /**
     * @phpstan-param class-string<AssetBundle> $fqcn
     */
    private static function prepareAsset(
        string $fqcn,
        ?string $css = null,
        ?string $js = null,
    ): void {
        $view = Yii::$app->view;
        if ($view && $view instanceof View) {
            $view->registerAssetBundle($fqcn);

            if ($css !== null) {
                $view->registerCss($css);
            }

            if ($js !== null) {
                $view->registerJs($js);
            }
        }
    }
}
