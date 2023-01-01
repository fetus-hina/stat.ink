<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\ostatus;

use Yii;
use app\components\helpers\BattleAtom;
use app\models\User;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

use function trim;

class FeedAction extends BaseAction
{
    public $screen_name;

    public function run()
    {
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->language = 'ja-JP';

        if (!$user = $this->getUser()) {
            return $this->http404();
        }

        if (!$battleId = Yii::$app->getRequest()->get('battle')) {
            $resp = Yii::$app->getResponse();
            $resp->format = 'raw';
            $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
            $resp->data = BattleAtom::createUserFeed($user);
            $resp->content = null;
            return $resp;
        } elseif (!$battle = $user->getBattles()->andWhere(['id' => $battleId])->one()) {
            return $this->http404();
        } else {
            $resp = Yii::$app->getResponse();
            $resp->format = 'raw';
            $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
            $resp->data = BattleAtom::createBattleFeed($user, $battle);
            $resp->content = null;
            return $resp;
        }
    }

    public function getUser(): ?User
    {
        $screenName = trim((string)Yii::$app->getRequest()->get('screen_name'));
        return User::findOne(['screen_name' => $screenName]);
    }

    public function http404(): Response
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = 404;
        $resp->statusText = 'Not Found';
        $resp->data = ['error' => Yii::t('yii', 'Page not found.')];
        $resp->content = null;
        return $resp;
    }
}
