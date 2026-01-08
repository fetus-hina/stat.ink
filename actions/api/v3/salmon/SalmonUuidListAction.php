<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\salmon;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\models\Salmon3;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

use const SORT_DESC;

final class SalmonUuidListAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit('compact-json');
    }

    public function run(): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user instanceof IdentityInterface) {
            throw new UnauthorizedHttpException();
        }

        $resp = Yii::$app->response;
        $resp->statusCode = 200;
        $resp->content = null;
        $resp->data = ArrayHelper::getColumn(
            Salmon3::find()
                ->andWhere([
                    'is_deleted' => false,
                    'user_id' => $user->id,
                ])
                ->andWhere(['not', ['client_uuid' => null]])
                ->orderBy(['id' => SORT_DESC])
                ->limit(500)
                ->all(),
            'client_uuid',
        );
        return $resp;
    }
}
