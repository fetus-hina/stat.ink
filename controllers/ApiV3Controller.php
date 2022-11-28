<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\api\v3\AbilityAction;
use app\actions\api\v3\DeleteBattleAction;
use app\actions\api\v3\GetSingleBattleAction;
use app\actions\api\v3\LobbyAction;
use app\actions\api\v3\PostBattleAction;
use app\actions\api\v3\RankAction;
use app\actions\api\v3\RuleAction;
use app\actions\api\v3\StageAction;
use app\actions\api\v3\VersionAction;
use app\actions\api\v3\WeaponAction;
use app\actions\api\v3\s3s\UsageAction;
use app\actions\api\v3\s3s\UuidListAction;
use app\actions\api\v3\salmon\DeleteSalmonAction;
use app\actions\api\v3\salmon\GetSingleSalmonAction;
use app\actions\api\v3\salmon\PostSalmonAction;
use app\actions\api\v3\salmon\SalmonUuidListAction;
use app\actions\api\v3\salmon\SalmonWeaponAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;

final class ApiV3Controller extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        parent::init();
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-battle' => ['delete'],
                    'delete-salmon' => ['delete'],
                    'post-battle' => ['post', 'put'],
                    'post-salmon' => ['post', 'put'],
                    '*' => ['head', 'get'],
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => [
                    'delete-battle',
                    'delete-salmon',
                    'post-battle',
                    'post-salmon',
                    's3s-uuid-list',
                    'salmon-uuid-list',
                    'single-battle',
                    'single-salmon',
                ],
                'optional' => [
                    'single-battle',
                    'single-salmon',
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'ability' => AbilityAction::class,
            'delete-battle' => DeleteBattleAction::class,
            'delete-salmon' => DeleteSalmonAction::class,
            'lobby' => LobbyAction::class,
            'post-battle' => PostBattleAction::class,
            'post-salmon' => PostSalmonAction::class,
            'rank' => RankAction::class,
            'rule' => RuleAction::class,
            's3s-usage' => UsageAction::class,
            's3s-uuid-list' => UuidListAction::class,
            'salmon-uuid-list' => SalmonUuidListAction::class,
            'salmon-weapon' => SalmonWeaponAction::class,
            'single-battle' => GetSingleBattleAction::class,
            'single-salmon' => GetSingleSalmonAction::class,
            'stage' => StageAction::class,
            'version' => VersionAction::class,
            'weapon' => WeaponAction::class,
        ];
    }
}
