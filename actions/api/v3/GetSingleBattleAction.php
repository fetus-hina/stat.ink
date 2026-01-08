<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\components\helpers\UuidRegexp;
use app\models\Battle3;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function preg_match;

final class GetSingleBattleAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    public function run(string $uuid, bool $full = false): Response
    {
        if (!preg_match(UuidRegexp::get(true), $uuid)) {
            throw new BadRequestHttpException();
        }

        $model = Battle3::find()
            ->with(
                ArrayHelper::toFlatten([
                    [
                        'battlePlayer3s',
                        'battlePlayer3s.crown',
                        'battlePlayer3s.species',
                        'battlePlayer3s.splashtagTitle',
                        'battlePlayer3s.weapon',
                        'battlePlayer3s.weapon.canonical',
                        'battlePlayer3s.weapon.mainweapon',
                        'battlePlayer3s.weapon.mainweapon.type',
                        'battlePlayer3s.weapon.special',
                        'battlePlayer3s.weapon.subweapon',
                        'battlePlayer3s.weapon.weapon3Aliases',
                    ],
                    array_map(
                        fn (string $base): array => [
                            "battlePlayer3s.{$base}",
                            "battlePlayer3s.{$base}.ability",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s.ability",
                        ],
                        ['clothing', 'headgear', 'shoes'],
                    ),
                ]),
            )
            ->andWhere([
                'is_deleted' => false,
                'uuid' => $uuid,
            ])
            ->limit(1)
            ->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = $model->user;
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $isAuthenticated = Yii::$app->user->isGuest
            ? false
            : (int)$user->id === (int)Yii::$app->user->id;

        $resp = Yii::$app->response;
        $resp->headers->set('Access-Control-Allow-Origin', '*');
        $resp->data = BattleApiFormatter::toJson(
            $model,
            fullTranslate: $full,
            isAuthenticated: $isAuthenticated,
        );

        return $resp;
    }
}
