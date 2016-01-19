<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\BattleFilterForm;
use app\models\Battle;
use app\models\User;

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
            $next[0] = 'show/user';
            $this->controller->redirect(Url::to($next));
            return;
        }

        $battle = Battle::find()
            ->with([
                'lobby',
                'rule',
                'rule.mode',
                'map',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'rank',
                'rankAfter',
            ]);

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        if ($filter->validate()) {
            $battle->filter($filter);
        }
        $summary = $battle->summary;

        $permLink = Url::to(
            array_merge(
                ['show/user', 'screen_name' => $user->screen_name],
                $filter->hasErrors() ? [] : $filter->toPermLink()
            ),
            true
        );

        $isPjax = $request->isPjax;
        $template = $this->viewMode === 'simple' ? 'user.simple.tpl' : 'user.tpl';
        return $this->controller->render($template, [
            'user'      => $user,
            'battleDataProvider' => new ActiveDataProvider([
                'query' => $battle,
                'pagination' => ['pageSize' => 100 ]
            ]),
            'summary'   => $summary,
            'filter'    => $filter,
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
