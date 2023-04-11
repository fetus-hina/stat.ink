<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use Yii;
use app\actions\show\v3\stats\badge\DataCreator;
use app\components\helpers\TypeHelper;
use app\models\User;
use app\models\UserBadge3Adjust;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Transaction;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use yii\web\Response;

use function array_merge;
use function filter_var;
use function is_int;
use function is_scalar;
use function preg_match;
use function vsprintf;

use const FILTER_VALIDATE_INT;

final class CorrectionBadgeAction extends Action
{
    use DataCreator;

    public function run(): Response|string
    {
        if (!$user = Yii::$app->user->identity) {
            throw new ForbiddenHttpException();
        }

        $user = TypeHelper::instanceOf($user, User::class);
        $req = TypeHelper::instanceOf(Yii::$app->request, Request::class);
        if ($req->get('screen_name') !== $user->screen_name) {
            return TypeHelper::instanceOf($this->controller, Controller::class)
                ->redirect(
                    ['show-v3/stats-correction-badge',
                        'screen_name' => $user->screen_name,
                    ],
                );
        }

        if ($req->isPost) {
            $newData = [];
            foreach ($req->post() as $k => $v) {
                $v = is_scalar($v) ? filter_var($v, FILTER_VALIDATE_INT) : false;
                if (
                    is_int($v) &&
                    $v !== 0 &&
                    preg_match('/^(?:rule|special|salmon)-/', (string)$k)
                ) {
                    $newData[$k] = $v;
                }
            }

            Yii::$app->db->transaction(
                function (Connection $db) use ($newData, $user): void {
                    if (!$newData) {
                        UserBadge3Adjust::deleteAll(['user_id' => $user->id]);
                        return;
                    }

                    $model = UserBadge3Adjust::find()->andWhere(['user_id' => $user->id])->limit(1)->one()
                        ?? Yii::createObject([
                            'class' => UserBadge3Adjust::class,
                            'user_id' => $user->id,
                        ]);
                    $model->data = new Expression(
                        vsprintf('%s::jsonb', [
                            $db->quoteValue(
                                Json::encode($newData),
                            ),
                        ]),
                    );
                    $model->save();
                },
                Transaction::READ_COMMITTED,
            );

            return TypeHelper::instanceOf($this->controller, Controller::class)
                ->redirect(
                    ['show-v3/stats-badge',
                        'screen_name' => $user->screen_name,
                    ],
                );
        }

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render(
                'stats/badge',
                array_merge($this->createData($user), [
                    'isEditable' => true,
                    'isEditing' => true,
                ]),
            );
    }
}
