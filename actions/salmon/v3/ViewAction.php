<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3;

use Yii;
use app\components\helpers\UuidRegexp;
use app\models\Salmon3;
use yii\base\Action;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function assert;
use function preg_match;

use const SORT_ASC;
use const SORT_DESC;

final class ViewAction extends Action
{
    /**
     * @return string|Response
     */
    public function run(string $screen_name, string $battle)
    {
        if (!preg_match(UuidRegexp::get(true), $battle)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = Salmon3::find()
            ->andWhere([
                'uuid' => $battle,
                'is_deleted' => false,
            ])
            ->limit(1)
            ->one();
        if (!$model || !$model->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $c = $this->controller;
        assert($c instanceof Controller);

        if ($model->user->screen_name !== $screen_name) {
            return $c->redirect(['salmon-v3/view',
                'screen_name' => $model->user->screen_name,
                'battle' => $model->uuid,
            ]);
        }

        return $c->render('view', [
            'model' => $model,
            'nextBattle' => $this->getNext($model),
            'prevBattle' => $this->getPrev($model),
        ]);
    }

    private function getNext(Salmon3 $current): ?Salmon3
    {
        return Salmon3::find()
            ->andWhere([
                'user_id' => $current->user_id,
                'is_deleted' => false,
            ])
            ->andWhere(['or',
                ['>', 'start_at', $current->start_at],
                ['and',
                    ['start_at' => $current->start_at],
                    ['>', 'id', $current->id],
                ],
            ])
            ->orderBy([
                'start_at' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->limit(1)
            ->one();
    }

    private function getPrev(Salmon3 $current): ?Salmon3
    {
        return Salmon3::find()
            ->andWhere([
                'user_id' => $current->user_id,
                'is_deleted' => false,
            ])
            ->andWhere(['or',
                ['<', 'start_at', $current->start_at],
                ['and',
                    ['start_at' => $current->start_at],
                    ['<', 'id', $current->id],
                ],
            ])
            ->orderBy([
                'start_at' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->limit(1)
            ->one();
    }
}
