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
use yii\base\Action;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

use function array_merge;
use function is_string;

final class BadgeAction extends Action
{
    use DataCreator;

    public function run(): string
    {
        $screenName = TypeHelper::instanceOf(Yii::$app->request, Request::class)->get('screen_name');
        $user = User::find()
            ->andWhere(
                is_string($screenName)
                    ? ['screen_name' => $screenName]
                    : '0 = 1',
            )
            ->limit(1)
            ->one();
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render(
                'stats/badge',
                array_merge($this->createData($user), [
                    'isEditable' => Yii::$app->user->identity?->id === $user->id,
                    'isEditing' => false,
                ]),
            );
    }
}
