<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

class Salmon2DeleteForm extends Model
{
    public $model;
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

        if (!$this->model->isEditable) {
            throw new ServerErrorHttpException('User mismatch and logic error');
        }

        return !!$this->model->delete();
    }
}
