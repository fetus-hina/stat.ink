<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
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
                    ])
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
            true
        );

        $battle = Battle2::find()
            ->with([
                'user',
                'mode',
                'lobby',
                'rule',
                'map',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'agent',
            ])
            ->andWhere(['user_id' => $user->id])
            ->orderBy(['battle2.id' => SORT_DESC]);

        $filter = Yii::createObject(Battle2FilterForm::class);
        if ($filter->load($_GET) && $filter->validate()) {
            $battle->applyFilter($filter);
            $permLink = Url::to(
                array_merge($filter->toPermLink(), ['show-v2/user', 'screen_name' => $user->screen_name]),
                true
            );
        }

        $summary = $battle->summary;


        $isPjax = $request->isPjax;
        $template = $this->viewMode === 'simple' ? 'user.simple.php' : 'user';
        return $this->controller->render($template, [
            'user'      => $user,
            'filter'    => $filter,
            'battleDataProvider' => new ActiveDataProvider([
                'query' => $battle,
                'pagination' => ['pageSize' => 100 ]
            ]),
            'summary'   => $summary,
            'permLink'  => $permLink,
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
        $ua = $request->userAgent;
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
