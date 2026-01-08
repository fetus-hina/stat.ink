<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2;
use app\models\Battle2FilterForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_filter;
use function array_merge;
use function explode;
use function implode;
use function in_array;
use function sprintf;
use function strpos;
use function substr;
use function time;

use const ARRAY_FILTER_USE_KEY;
use const SORT_DESC;

class UserAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        // リスト表示モード切替
        if ($request->get('v') != '') {
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
            $next[0] = 'show-v2/user';
            $this->controller->redirect(Url::to($next));
            return;
        }

        $permLink = Url::to(
            ['show-v2/user', 'screen_name' => $user->screen_name],
            true,
        );

        $battle = Battle2::find()
            ->withFreshness()
            ->with([
                'agent',
                'festTitle',
                'festTitleAfter',
                'freshnessModel',
                'gender',
                'lobby',
                'map',
                'mode',
                'myTeamFestTheme',
                'rank',
                'rankAfter',
                'rule',
                'specialBattle',
                'user',
                'version',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere(['user_id' => $user->id])
            ->orderBy(['battle2.id' => SORT_DESC]);

        $filter = Yii::createObject(Battle2FilterForm::class);
        $filter->screen_name = $user->screen_name;
        $filter->load($_GET);
        if ($filter->validate()) {
            // id_from, id_to が指定されているときは filter に id:<from>-<to> をセットして
            // リダイレクトする
            if ($filter->id_from && $filter->id_to) {
                $tmp = explode(' ', (string)$filter->filter);
                $tmp = array_filter($tmp);
                $tmp = array_filter($tmp, fn (string $value): bool => substr($value, 0, 3) !== 'id:');
                $tmp[] = sprintf('id:%d-%d', (int)$filter->id_from, (int)$filter->id_to);
                $filter->filter = implode(' ', $tmp);

                $next = [
                    'show-v2/user',
                    'screen_name' => $user->screen_name,
                    'filter' => array_filter(
                        $filter->attributes,
                        fn (string $key): bool => !in_array($key, ['screen_name', 'id_from', 'id_to'], true),
                        ARRAY_FILTER_USE_KEY,
                    ),
                ];
                $this->controller->redirect(Url::to($next, true));
                return;
            }

            $battle->applyFilter($filter);
            $permLink = Url::to(
                array_merge(
                    $filter->toPermLink(),
                    ['show-v2/user', 'screen_name' => $user->screen_name],
                ),
                true,
            );
        }

        $summary = $battle->summary;

        $template = $this->viewMode === 'simple' ? 'user.simple.php' : 'user';
        return $this->controller->render($template, [
            'user' => $user,
            'filter' => $filter,
            'battleDataProvider' => Yii::createObject([
                'class' => ActiveDataProvider::class,
                'query' => $battle,
                'pagination' => [
                    'pageSize' => 100,
                ],
                'sort' => false,
            ]),
            'summary' => $summary,
            'permLink' => $permLink,
        ]);
    }

    public function getViewMode()
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
