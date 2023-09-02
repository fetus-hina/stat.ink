<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\AppLinkAsset;
use app\assets\BootstrapIconsAsset;
use app\assets\MedalAsset;
use app\assets\s3PixelIcons\AbilityIconAsset;
use app\assets\s3PixelIcons\LobbyIconAsset;
use app\assets\s3PixelIcons\RuleIconAsset;
use app\assets\s3PixelIcons\SalmometerIconAsset;
use app\assets\s3PixelIcons\SalmonModeIconAsset;
use app\assets\s3PixelIcons\SpecialIconAsset;
use app\assets\s3PixelIcons\SubweaponIconAsset;
use app\assets\s3PixelIcons\UiIconAsset;
use app\assets\s3PixelIcons\VersionIconAsset;
use app\assets\s3PixelIcons\WeaponIconAsset;
use app\components\helpers\TypeHelper;
use app\models\Ability3;
use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Rule3;
use app\models\SalmonWeapon3;
use app\models\Special3;
use app\models\Subweapon3;
use app\models\Weapon3;
use yii\base\UnknownMethodException;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use yii\web\View;

use function array_filter;
use function array_values;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function mb_chr;
use function str_starts_with;

/**
 * @method static string addSomething()
 * @method static string android()
 * @method static string apiJson()
 * @method static string appLink()
 * @method static string appUnlink()
 * @method static string arrowRight()
 * @method static string back()
 * @method static string blog()
 * @method static string bluesky()
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
 * @method static string mastodon()
 * @method static string nextPage()
 * @method static string no()
 * @method static string number();
 * @method static string ok()
 * @method static string permalink()
 * @method static string popup()
 * @method static string powerEgg()
 * @method static string prevPage()
 * @method static string refresh()
 * @method static string s3AbilityAbilityDoubler
 * @method static string s3AbilityComeback
 * @method static string s3AbilityDropRoller
 * @method static string s3AbilityHaunt
 * @method static string s3AbilityInkRecoveryUp
 * @method static string s3AbilityInkResistanceUp
 * @method static string s3AbilityInkSaverMain
 * @method static string s3AbilityInkSaverSub
 * @method static string s3AbilityIntensifyAction
 * @method static string s3AbilityLastDitchEffort
 * @method static string s3AbilityNinjaSquid
 * @method static string s3AbilityObjectShredder
 * @method static string s3AbilityOpeningGambit
 * @method static string s3AbilityQuickRespawn
 * @method static string s3AbilityQuickSuperJump
 * @method static string s3AbilityRespawnPunisher
 * @method static string s3AbilityRunSpeedUp
 * @method static string s3AbilitySpecialChargeUp
 * @method static string s3AbilitySpecialPowerUp
 * @method static string s3AbilitySpecialSaver
 * @method static string s3AbilityStealthJump
 * @method static string s3AbilitySubPowerUp
 * @method static string s3AbilitySubResistanceUp
 * @method static string s3AbilitySwimSpeedUp
 * @method static string s3AbilityTenacity
 * @method static string s3AbilityThermalInk
 * @method static string s3AbilityUnknown()
 * @method static string s3BigRun()
 * @method static string s3Death()
 * @method static string s3Eggstra()
 * @method static string s3Kill()
 * @method static string s3LobbyBankara()
 * @method static string s3LobbyEvent()
 * @method static string s3LobbyPrivate()
 * @method static string s3LobbyRegular()
 * @method static string s3LobbySplatfest()
 * @method static string s3LobbyX()
 * @method static string s3Rescued()
 * @method static string s3Rescues()
 * @method static string s3RuleArea()
 * @method static string s3RuleAsari()
 * @method static string s3RuleHoko()
 * @method static string s3RuleNawabari()
 * @method static string s3RuleTricolor()
 * @method static string s3RuleYagura()
 * @method static string s3Salmometer0()
 * @method static string s3Salmometer1()
 * @method static string s3Salmometer2()
 * @method static string s3Salmometer3()
 * @method static string s3Salmometer4()
 * @method static string s3Salmometer5()
 * @method static string s3Salmon()
 * @method static string s3Signal()
 * @method static string s3TricolorAttacker()
 * @method static string s3TricolorDefender()
 * @method static string scrollTo()
 * @method static string search()
 * @method static string silverMedal()
 * @method static string silverScale()
 * @method static string slack()
 * @method static string sortable()
 * @method static string splatoon1()
 * @method static string splatoon2()
 * @method static string splatoon3()
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
        'mastodon' => 'mastodon',
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
     * @var array<string, array{
     *   class-string<AssetBundle>,
     *   string,
     *   string|array{string, string, ?array},
     *   bool|string|array{string, string, ?array}|null
     * }>
     */
    private static $assetImageMap = [
        'bluesky' => [AppLinkAsset::class, 'bluesky.png'],
        'goldenEgg' => [UiIconAsset::class, 'golden_egg.png', ['app-salmon2', 'Golden Eggs'], true],
        'powerEgg' => [UiIconAsset::class, 'power_egg.png', ['app-salmon2', 'Power Eggs'], true],
        's3AbilityAbilityDoubler' => [AbilityIconAsset::class, 'ability_doubler.png'],
        's3AbilityComeback' => [AbilityIconAsset::class, 'comeback.png'],
        's3AbilityDropRoller' => [AbilityIconAsset::class, 'drop_roller.png'],
        's3AbilityHaunt' => [AbilityIconAsset::class, 'haunt.png'],
        's3AbilityInkRecoveryUp' => [AbilityIconAsset::class, 'ink_recovery_up.png'],
        's3AbilityInkResistanceUp' => [AbilityIconAsset::class, 'ink_resistance_up.png'],
        's3AbilityInkSaverMain' => [AbilityIconAsset::class, 'ink_saver_main.png'],
        's3AbilityInkSaverSub' => [AbilityIconAsset::class, 'ink_saver_sub.png'],
        's3AbilityIntensifyAction' => [AbilityIconAsset::class, 'intensify_action.png'],
        's3AbilityLastDitchEffort' => [AbilityIconAsset::class, 'last_ditch_effort.png'],
        's3AbilityNinjaSquid' => [AbilityIconAsset::class, 'ninja_squid.png'],
        's3AbilityObjectShredder' => [AbilityIconAsset::class, 'object_shredder.png'],
        's3AbilityOpeningGambit' => [AbilityIconAsset::class, 'opening_gambit.png'],
        's3AbilityQuickRespawn' => [AbilityIconAsset::class, 'quick_respawn.png'],
        's3AbilityQuickSuperJump' => [AbilityIconAsset::class, 'quick_super_jump.png'],
        's3AbilityRespawnPunisher' => [AbilityIconAsset::class, 'respawn_punisher.png'],
        's3AbilityRunSpeedUp' => [AbilityIconAsset::class, 'run_speed_up.png'],
        's3AbilitySpecialChargeUp' => [AbilityIconAsset::class, 'special_charge_up.png'],
        's3AbilitySpecialPowerUp' => [AbilityIconAsset::class, 'special_power_up.png'],
        's3AbilitySpecialSaver' => [AbilityIconAsset::class, 'special_saver.png'],
        's3AbilityStealthJump' => [AbilityIconAsset::class, 'stealth_jump.png'],
        's3AbilitySubPowerUp' => [AbilityIconAsset::class, 'sub_power_up.png'],
        's3AbilitySubResistanceUp' => [AbilityIconAsset::class, 'sub_resistance_up.png'],
        's3AbilitySwimSpeedUp' => [AbilityIconAsset::class, 'swim_speed_up.png'],
        's3AbilityTenacity' => [AbilityIconAsset::class, 'tenacity.png'],
        's3AbilityThermalInk' => [AbilityIconAsset::class, 'thermal_ink.png'],
        's3AbilityUnknown' => [AbilityIconAsset::class, 'unknown.png'],
        's3BigRun' => [SalmonModeIconAsset::class, 'bigrun.png', ['app-salmon3', 'Big Run'], true],
        's3Death' => [UiIconAsset::class, 'death.png', ['app', 'Deaths'], true],
        's3Eggstra' => [SalmonModeIconAsset::class, 'eggstra.png', ['app-salmon3', 'Eggstra Work'], true],
        's3Kill' => [UiIconAsset::class, 'kill.png', ['app', 'Kills'], true],
        's3LobbyBankara' => [LobbyIconAsset::class, 'bankara.png', ['app-lobby3', 'Anarchy Battle'], true],
        's3LobbyEvent' => [LobbyIconAsset::class, 'event.png', ['app-lobby3', 'Challenge'], true],
        's3LobbyPrivate' => [LobbyIconAsset::class, 'private.png', ['app-lobby3', 'Private Battle'], true],
        's3LobbyRegular' => [LobbyIconAsset::class, 'regular.png', ['app-lobby3', 'Regular Battle'], true],
        's3LobbySplatfest' => [LobbyIconAsset::class, 'splatfest.png', ['app-lobby3', 'Splatfest'], true],
        's3LobbyX' => [LobbyIconAsset::class, 'xmatch.png', ['app-lobby3', 'X Battle'], true],
        's3Rescued' => [UiIconAsset::class, 'rescued.png', ['app-salmon3', 'Rescued'], true],
        's3Rescues' => [UiIconAsset::class, 'rescues.png', ['app-salmon3', 'Rescues'], true],
        's3RuleArea' => [RuleIconAsset::class, 'area.png', ['app-rule3', 'Splat Zones'], true],
        's3RuleAsari' => [RuleIconAsset::class, 'asari.png', ['app-rule3', 'Clam Blitz'], true],
        's3RuleHoko' => [RuleIconAsset::class, 'hoko.png', ['app-rule3', 'Rainmaker'], true],
        's3RuleNawabari' => [RuleIconAsset::class, 'nawabari.png', ['app-rule3', 'Turf War'], true],
        's3RuleTricolor' => [RuleIconAsset::class, 'tricolor.png', ['app-rule3', 'Tricolor Turf War'], true],
        's3RuleYagura' => [RuleIconAsset::class, 'yagura.png', ['app-rule3', 'Tower Control'], true],
        's3Salmometer0' => [SalmometerIconAsset::class, 'salmometer-0.png', '(0/5)', '0 / 5'],
        's3Salmometer1' => [SalmometerIconAsset::class, 'salmometer-1.png', '(1/5)', '1 / 5'],
        's3Salmometer2' => [SalmometerIconAsset::class, 'salmometer-2.png', '(2/5)', '2 / 5'],
        's3Salmometer3' => [SalmometerIconAsset::class, 'salmometer-3.png', '(3/5)', '3 / 5'],
        's3Salmometer4' => [SalmometerIconAsset::class, 'salmometer-4.png', '(4/5)', '4 / 5'],
        's3Salmometer5' => [SalmometerIconAsset::class, 'salmometer-5.png', '(5/5)', '5 / 5'],
        's3Salmon' => [SalmonModeIconAsset::class, 'salmon.png', ['app-salmon2', 'Salmon Run'], true],
        's3Signal' => [UiIconAsset::class, 'signal.png', ['app', 'Ultra Signals'], ['app', 'Try to secure the Ultra Signal']],
        's3TricolorAttacker' => [RuleIconAsset::class, 'tricolor-attacker.png', ['app-rule3', 'Attackers'], true],
        's3TricolorDefender' => [RuleIconAsset::class, 'tricolor-defender.png', ['app-rule3', 'Defenders'], true],
        'splatoon1' => [VersionIconAsset::class, 's1.png', '[1]', ['app', 'Splatoon']],
        'splatoon2' => [VersionIconAsset::class, 's2.png', '[2]', ['app', 'Splatoon 2']],
        'splatoon3' => [VersionIconAsset::class, 's3.png', '[3]', ['app', 'Splatoon 3']],
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

    public static function s3Ability(Ability3|string|null $ability): ?string
    {
        return match ($ability instanceof Ability3 ? $ability->key : $ability) {
            'ability_doubler' => self::s3AbilityAbilityDoubler(),
            'comeback' => self::s3AbilityComeback(),
            'drop_roller' => self::s3AbilityDropRoller(),
            'haunt' => self::s3AbilityHaunt(),
            'ink_recovery_up' => self::s3AbilityInkRecoveryUp(),
            'ink_resistance_up' => self::s3AbilityInkResistanceUp(),
            'ink_saver_main' => self::s3AbilityInkSaverMain(),
            'ink_saver_sub' => self::s3AbilityInkSaverSub(),
            'intensify_action' => self::s3AbilityIntensifyAction(),
            'last_ditch_effort' => self::s3AbilityLastDitchEffort(),
            'ninja_squid' => self::s3AbilityNinjaSquid(),
            'object_shredder' => self::s3AbilityObjectShredder(),
            'opening_gambit' => self::s3AbilityOpeningGambit(),
            'quick_respawn' => self::s3AbilityQuickRespawn(),
            'quick_super_jump' => self::s3AbilityQuickSuperJump(),
            'respawn_punisher' => self::s3AbilityRespawnPunisher(),
            'run_speed_up' => self::s3AbilityRunSpeedUp(),
            'special_charge_up' => self::s3AbilitySpecialChargeUp(),
            'special_power_up' => self::s3AbilitySpecialPowerUp(),
            'special_saver' => self::s3AbilitySpecialSaver(),
            'stealth_jump' => self::s3AbilityStealthJump(),
            'sub_power_up' => self::s3AbilitySubPowerUp(),
            'sub_resistance_up' => self::s3AbilitySubResistanceUp(),
            'swim_speed_up' => self::s3AbilitySwimSpeedUp(),
            'tenacity' => self::s3AbilityTenacity(),
            'thermal_ink' => self::s3AbilityThermalInk(),
            'unknown' => self::s3AbilityUnknown(),
            default => null,
        };
    }

    public static function s3Lobby(Lobby3|LobbyGroup3|string|null $lobby): ?string
    {
        $lobby = match (true) {
            $lobby instanceof LobbyGroup3 => $lobby->key,
            $lobby instanceof Lobby3 => $lobby->key,
            default => $lobby,
        };

        return match ($lobby) {
            'bankara', 'bankara_challenge', 'bankara_open' => self::s3LobbyBankara(),
            'event' => self::s3LobbyEvent(),
            'private' => self::s3LobbyPrivate(),
            'regular' => self::s3LobbyRegular(),
            'splatfest', 'splatfest_challenge', 'splatfest_open' => self::s3LobbySplatfest(),
            'xmatch' => self::s3LobbyX(),
            default => null,
        };
    }

    public static function s3Rule(Rule3|string|null $rule): ?string
    {
        return match ($rule instanceof Rule3 ? $rule->key : $rule) {
            'area' => self::s3RuleArea(),
            'asari' => self::s3RuleAsari(),
            'hoko' => self::s3RuleHoko(),
            'nawabari' => self::s3RuleNawabari(),
            'tricolor' => self::s3RuleTricolor(),
            'yagura' => self::s3RuleYagura(),
            default => null,
        };
    }

    public static function s3Salmometer(?int $level = null): ?string
    {
        return match ($level) {
            null => self::assetImage(
                SalmometerIconAsset::class,
                'salmometer-5.png',
                alt: null,
                title: false,
            ),
            0 => self::s3Salmometer0(),
            1 => self::s3Salmometer1(),
            2 => self::s3Salmometer2(),
            3 => self::s3Salmometer3(),
            4 => self::s3Salmometer4(),
            5 => self::s3Salmometer5(),
            default => null,
        };
    }

    public static function s3Subweapon(Subweapon3|string|null $model, ?string $size = null): ?string
    {
        if ($model === null) {
            return null;
        }

        if (is_string($model)) {
            $model = Subweapon3::find()
                ->andWhere(['key' => $model])
                ->limit(1)
                ->cache(14 * 86400)
                ->one();
            if (!$model) {
                return null;
            }
        }

        return self::assetImage(
            SubweaponIconAsset::class,
            "{$model->key}.png",
            Yii::t('app-subweapon3', $model->name),
            true,
            $size,
        );
    }

    public static function s3Special(Special3|string|null $model, ?string $size = null): ?string
    {
        if ($model === null) {
            return null;
        }

        if (is_string($model)) {
            $model = Special3::find()
                ->andWhere(['key' => $model])
                ->limit(1)
                ->cache(14 * 86400)
                ->one();
            if (!$model) {
                return null;
            }
        }

        return self::assetImage(
            SpecialIconAsset::class,
            "{$model->key}.png",
            Yii::t('app-special3', $model->name),
            true,
            $size,
        );
    }

    public static function s3Weapon(
        Weapon3|SalmonWeapon3|string|null $weapon,
        ?string $size = null,
    ): ?string {
        $key = match (true) {
            $weapon instanceof Weapon3, $weapon instanceof SalmonWeapon3 => $weapon->key,
            default => $weapon,
        };
        if (!is_string($key)) {
            return null;
        }

        $directory = match (true) {
            in_array($key, [
                'hotblaster',
                'clashblaster_neo',
                'clashblaster',
                'nova_neo',
                'nova',
                'longblaster',
                'rapid_deco',
                'rapid_elite_deco',
                'rapid_elite',
                'rapid',
                'sblast92',
            ], true) => 'Blasters',
            in_array($key, [
                'campingshelter',
                'campingshelter_sorella',
                'parashelter',
                'parashelter_sorella',
                'spygadget',
            ], true) => 'Brellas',
            in_array($key, [
                'fincent',
                'hokusai',
                'hokusai_hue',
                'pablo',
                'pablo_hue',
            ], true) => 'Brushes',
            in_array($key, [
                'bamboo14mk1',
                'liter4k',
                'liter4k_scope',
                'rpen_5h',
                'soytuber',
                'soytuber_custom',
                'splatcharger',
                'splatcharger_collabo',
                'splatscope',
                'splatscope_collabo',
                'squiclean_a',
            ], true) => 'Chargers',
            in_array($key, [
                'dualsweeper',
                'dualsweeper_custom',
                'kelvin525',
                'maneuver',
                'quadhopper_black',
                'quadhopper_white',
                'sputtery',
                'sputtery_hue',
            ], true) => 'Dualies',
            in_array($key, [
                'carbon',
                'carbon_deco',
                'dynamo',
                'dynamo_tesla',
                'splatroller',
                'splatroller_collabo',
                'variableroller',
                'wideroller',
                'wideroller_collabo',
            ], true) => 'Rollers',
            in_array($key, [
                '52gal',
                '96gal',
                '96gal_deco',
                'bold',
                'bold_neo',
                'bottlegeyser',
                'h3reelgun',
                'h3reelgun_d',
                'heroshooter_replica',
                'jetsweeper',
                'jetsweeper_custom',
                'l3reelgun',
                'l3reelgun_d',
                'momiji',
                'nzap85',
                'nzap89',
                'prime',
                'prime_collabo',
                'promodeler_mg',
                'promodeler_rg',
                'sharp',
                'sharp_neo',
                'spaceshooter',
                'spaceshooter_collabo',
                'sshooter',
                'sshooter_collabo',
                'wakaba',
            ], true) => 'Shooters',
            in_array($key, [
                'bucketslosher',
                'bucketslosher_deco',
                'explosher',
                'furo',
                'furo_deco',
                'hissen',
                'hissen_hue',
                'moprin',
                'screwslosher',
                'screwslosher_neo',
            ], true) => 'Sloshers',
            in_array($key, [
                'drivewiper',
                'drivewiper_deco',
                'jimuwiper',
            ], true) => 'Splatanas',
            in_array($key, [
                'barrelspinner',
                'barrelspinner_deco',
                'examiner',
                'hydra',
                'kugelschreiber',
                'kugelschreiber_hue',
                'nautilus47',
                'splatspinner',
                'splatspinner_collabo',
            ], true) => 'Splatlings',
            in_array($key, [
                'lact450',
                'tristringer',
                'tristringer_collabo',
            ], true) => 'Stringers',
            default => null,
        };

        if (!is_string($directory)) {
            return null;
        }

        if (
            !$weapon instanceof Weapon3 &&
            !$weapon instanceof SalmonWeapon3
        ) {
            $weapon = str_starts_with($key, 'kuma_')
                ? SalmonWeapon3::find()
                    ->andWhere(['key' => $key])
                    ->limit(1)
                    ->cache(86400)
                    ->one()
                : Weapon3::find()
                    ->andWhere(['key' => $key])
                    ->limit(1)
                    ->cache(86400)
                    ->one();
        }

        return self::assetImage(
            WeaponIconAsset::class,
            "{$directory}/{$key}.png",
            Yii::t('app-weapon3', (string)$weapon?->name),
            true,
            $size,
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
     * @param string|array{string, string, ?array}|null $alt
     * @param string|array{string, string, ?array}|bool|null $alt
     */
    private static function assetImage(
        string $assetClass,
        string $assetPath,
        array|string|null $alt = null,
        array|bool|string|null $title = null,
        ?string $size = null,
    ): string {
        // self::prepareAsset($assetClass);
        $am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);

        if (is_array($alt)) {
            $alt = Yii::t(
                TypeHelper::string($alt[0]),
                TypeHelper::string($alt[1]),
                $alt[2] ?? [],
            );
        }

        $title = match (true) {
            is_string($title) => $title,
            is_array($title) => Yii::t(
                TypeHelper::string($title[0]),
                TypeHelper::string($title[1]),
                $title[2] ?? [],
            ),
            $title === true && is_string($alt) => $alt,
            default => false,
        };

        $options = [
            'alt' => $alt ?? false,
            'class' => array_values(
                array_filter([
                    'basic-icon',
                    match (true) {
                        is_string($title) => 'auto-tooltip',
                        default => null,
                    },
                ]),
            ),
            'draggable' => 'false',
            'style' => [
                '--icon-height' => $size ?? '1em',
                '--icon-valign' => 'middle',
            ],
            'title' => $title,
        ];

        return Html::img($am->getAssetUrl($am->getBundle($assetClass), $assetPath), $options);
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
