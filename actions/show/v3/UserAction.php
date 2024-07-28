<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3;

use Yii;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\components\helpers\BattleSummarizer;
use app\models\Battle3;
use app\models\Battle3FilterForm;
use app\models\User;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function array_merge;
use function hash_hmac;
use function http_build_query;
use function strpos;
use function time;

use const SORT_DESC;

final class UserAction extends Action
{
    /**
     * @var Response::FORMAT_HTML|Response::FORMAT_JSON
     */
    public string $format = Response::FORMAT_HTML;

    public function run(): string|Response
    {
        $request = Yii::$app->request;
        $user = User::findOne([
            'screen_name' => (string)$request->get('screen_name'),
        ]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        // リスト表示モード切替
        if (
            $this->format !== Response::FORMAT_JSON &&
            (string)$request->get('v') !== ''
        ) {
            $view = $request->get('v');
            if ($view === 'simple' || $view === 'standard') {
                Yii::$app->response->cookies->add(
                    new Cookie([
                        'name' => 'battle-list',
                        'value' => $view,
                        'expire' => time() + 86400 * 366,
                    ]),
                );
            }

            $next = $_GET;
            unset($next['v']);
            $next[0] = 'show-v3/user';
            return $this->controller->redirect(Url::to($next));
        }

        $battle = Battle3::find()
            ->joinWith('result')
            ->with([
                'agent',
                'lobby',
                'map',
                'medals',
                'medals.canonical',
                'rankAfter',
                'rankBefore',
                'rule',
                'user',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere([
                '{{%battle3}}.[[user_id]]' => $user->id,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->orderBy([
                '{{%battle3}}.[[end_at]]' => SORT_DESC,
                '{{%battle3}}.[[id]]' => SORT_DESC,
            ]);

        $form = Yii::createObject(Battle3FilterForm::class);
        if ($form->load($_GET) && $form->validate()) {
            $form->decorateQuery($battle, $user);
        }

        $permLink = Url::to(
            array_merge(
                $form->toPermLink(),
                ['show-v3/user',
                    'screen_name' => $user->screen_name,
                ],
            ),
            true,
        );
        $template = $this->viewMode === 'simple' ? 'user.simple.php' : 'user';
        $battleDataProvider = Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $battle,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => false,
        ]);

        if ($this->format !== Response::FORMAT_JSON) {
            return $this->controller->render($template, [
                'battleDataProvider' => $battleDataProvider,
                'filter' => $form,
                'permLink' => $permLink,
                'summary' => BattleSummarizer::getSummary3($battle),
                'user' => $user,
            ]);
        }

        $battle->with(
            ArrayHelper::toFlatten([
                [
                    'agent',
                    'battleImageGear3',
                    'battleImageJudge3',
                    'battleImageResult3',
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
                    'festDragon',
                    'lobby',
                    'map',
                    'map.map3Aliases',
                    'medals',
                    'ourTeamRole',
                    'ourTeamTheme',
                    'rule',
                    'rule.rule3Aliases',
                    'theirTeamRole',
                    'theirTeamTheme',
                    'thirdTeamRole',
                    'thirdTeamTheme',
                    'variables',
                    'version',
                    'weapon',
                    'weapon.canonical',
                    'weapon.mainweapon',
                    'weapon.mainweapon.type',
                    'weapon.weapon3Aliases',
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
        );

        $isAuthenticated = false;
        $fullTranslate = (bool)$request->get('full');
        $cache = Yii::$app->cache;

        $response = Yii::$app->response;
        $response->format = 'compact-json';
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->data = array_map(
            fn (Battle3 $model): ?array => $cache->getOrSet(
                hash_hmac(
                    'sha256',
                    http_build_query([
                        'at' => $model->updated_at,
                        'battle' => $model->id,
                        'fullTranslate' => $fullTranslate ? 'yes' : 'no',
                        'isAuthenticated' => $isAuthenticated ? 'yes' : 'no',
                    ]),
                    __METHOD__,
                ),
                fn (): ?array => BattleApiFormatter::toJson(
                    model: $model,
                    isAuthenticated: $isAuthenticated,
                    fullTranslate: $fullTranslate,
                ),
                duration: 7200,
            ),
            $battleDataProvider->getModels(),
        );

        return $response;
    }

    public function getViewMode(): string
    {
        $request = Yii::$app->request;
        $mode = null;
        if ($cookie = $request->cookies->get('battle-list')) {
            $mode = $cookie->value;
        }
        if ($mode === 'simple' || $mode === 'standard') {
            return $mode;
        }
        $ua = (string)$request->userAgent;
        if (strpos($ua, 'iPod') !== false || strpos($ua, 'iPhone') !== false) {
            return 'simple';
        }
        if (strpos($ua, 'Android') !== false) {
            return 'simple';
        }
        if (strpos($ua, 'Windows Phone') !== false) {
            return 'simple';
        }
        return 'standard';
    }
}
