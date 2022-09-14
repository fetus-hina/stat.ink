<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3;

use Yii;
use app\components\helpers\BattleSummarizer;
use app\models\Battle3;
use app\models\User;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

final class UserAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $user = User::findOne([
            'screen_name' => (string)$request->get('screen_name'),
        ]);
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
            $next[0] = 'show-v3/user';
            $this->controller->redirect(Url::to($next));
            return;
        }

        $permLink = Url::to(
            ['show-v3/user', 'screen_name' => $user->screen_name],
            true
        );

        $battle = Battle3::find()
            ->joinWith('result')
            ->with([
                'agent',
                'lobby',
                'map',
                'rankAfter',
                'rankBefore',
                'rule',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->orderBy(['battle3.id' => SORT_DESC]);

        $permLink = Url::to(
            ['show-v3/user', 'screen_name' => $user->screen_name],
            true
        );
        $isPjax = $request->isPjax;
        $template = $this->viewMode === 'simple' ? 'user.simple.php' : 'user';
        return $this->controller->render($template, [
            'user' => $user,
            'battleDataProvider' => Yii::createObject([
                'class' => ActiveDataProvider::class,
                'query' => $battle,
                'pagination' => [
                    'pageSize' => 100,
                ],
                'sort' => false,
            ]),
            'summary' => BattleSummarizer::getSummary3($battle),
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
