<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "death_reason_type2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property DeathReason2[] $deathReasons
 */
class DeathReasonType2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'death_reason_type2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDeathReasons()
    {
        return $this->hasMany(DeathReason2::class, ['type_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-death2', $this->name),
        ];
    }
}
