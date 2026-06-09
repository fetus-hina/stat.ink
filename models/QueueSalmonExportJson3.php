<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "queue_salmon_export_json3".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $updated_at
 *
 * @property User $user
 */
class QueueSalmonExportJson3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'queue_salmon_export_json3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['id', 'user_id', 'updated_at'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['id'], 'string', 'max' => 36],
            [['user_id'], 'unique'],
            [['id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
