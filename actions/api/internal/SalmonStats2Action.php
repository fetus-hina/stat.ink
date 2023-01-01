<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Yii;
use app\models\SalmonStats2;
use app\models\User;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

use const SORT_DESC;

class SalmonStats2Action extends Action
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'compact-json';
    }

    public function run()
    {
        $input = $this->getInputForm();
        if ($input->hasErrors()) {
            throw new BadRequestHttpException();
        }

        $user = User::findOne(['screen_name' => (string)$input->screen_name]);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        return Yii::$app->db->transactionEx(function ($db) use ($user): array {
            $query = SalmonStats2::find()
                ->andWhere(['user_id' => $user->id])
                ->orderBy(['as_of' => SORT_DESC])
                ->limit(5000);

            $results = [];
            foreach ($query->each(200, $db) as $stats) {
                $results[] = $stats->toJsonArray();
            }
            return $results;
        });
    }

    public function getInputForm(): DynamicModel
    {
        $request = Yii::$app->request;
        return DynamicModel::validateData(
            [
                'screen_name' => $request->get('screen_name'),
            ],
            [
                [['screen_name'], 'trim'],
                [['screen_name'], 'required'],
                [['screen_name'], 'string'],
                [['screen_name'], 'match',
                    'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                ],
            ],
        );
    }
}
