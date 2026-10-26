<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

class Salmon2DeleteForm extends Model
{
    /**
     * @var Salmon2|null
     */
    public $model;
    /**
     * @var string|null
     */
    public $agree;

    public function rules()
    {
        $agreeErrorMessage = Yii::t('app-salmon2', 'You must agree to the above to delete this job.');
        return [
            [['agree'], 'required',
                'message' => $agreeErrorMessage,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'agree' => Yii::t('app', 'Agreement'),
        ];
    }

    public function delete(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$model = $this->model) {
            throw new ServerErrorHttpException('The job is not set');
        }

        if (!$model->isEditable) {
            throw new ServerErrorHttpException('User mismatch and logic error');
        }

        return !!$model->delete();
    }
}
