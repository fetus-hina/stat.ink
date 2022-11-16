<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_special_use3".
 *
 * @property integer $wave_id
 * @property integer $special_id
 * @property integer $count
 *
 * @property Special3 $special
 * @property SalmonWave3 $wave
 */
class SalmonSpecialUse3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_special_use3';
    }

    public function rules()
    {
        return [
            [['wave_id', 'special_id', 'count'], 'required'],
            [['wave_id', 'special_id', 'count'], 'default', 'value' => null],
            [['wave_id', 'special_id', 'count'], 'integer'],
            [['wave_id', 'special_id'], 'unique', 'targetAttribute' => ['wave_id', 'special_id']],
            [['wave_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWave3::class, 'targetAttribute' => ['wave_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'wave_id' => 'Wave ID',
            'special_id' => 'Special ID',
            'count' => 'Count',
        ];
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getWave(): ActiveQuery
    {
        return $this->hasOne(SalmonWave3::class, ['id' => 'wave_id']);
    }
}
