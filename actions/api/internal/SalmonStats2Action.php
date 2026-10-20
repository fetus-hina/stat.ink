<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Override;
use Yii;
use app\models\InternalSalmonStats2Form;
use app\models\SalmonStats2;
use app\models\User;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\components\helpers\TypeHelper;

use const SORT_DESC;

class SalmonStats2Action extends Action
{
    #[Override]
    public function init(): void
    {
        parent::init();
        Yii::$app->response->format = 'compact-json';
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function run(): ?array
    {
        $input = $this->getInputForm();
        if ($input->hasErrors()) {
            throw new BadRequestHttpException();
        }

        $user = User::findOne(['screen_name' => TypeHelper::string($input->screen_name)]);
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

    public function getInputForm(): InternalSalmonStats2Form
    {
        $request = Yii::$app->request;
        return InternalSalmonStats2Form::validateData(
            [
                'screen_name' => $request->get('screen_name'),
            ],
        );
    }
}
