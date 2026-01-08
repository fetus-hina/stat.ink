<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

class Battle2DeleteForm extends Model
{
    public $battle;
    public $agree;

    public function rules()
    {
        $agreeErrorMessage = Yii::t('app', 'You must agree to the above to delete this battle.');
        return [
            [['battle'], 'required'],
            [['agree'], 'required', 'message' => $agreeErrorMessage],
            [['battle'], 'exist',
                'targetClass' => Battle2::class,
                'targetAttribute' => 'id',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
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
        if (!$model = Battle2::findOne(['id' => $this->battle])) {
            return false;
        }
        if ($model->user_id != Yii::$app->user->id) {
            throw new ServerErrorHttpException('User mismatch and logic error');
        }
        return !!$model->delete();
    }
}
