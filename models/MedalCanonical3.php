<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "medal_canonical3".
 *
 * @property integer $id
 * @property string $key
 * @property boolean $gold
 * @property string $name
 *
 * @property Medal3[] $medal3s
 */
class MedalCanonical3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'medal_canonical3';
    }

    public function rules()
    {
        return [
            [['key', 'gold', 'name'], 'required'],
            [['gold'], 'boolean'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'gold' => 'Gold',
            'name' => 'Name',
        ];
    }

    public function getMedal3s(): ActiveQuery
    {
        return $this->hasMany(Medal3::class, ['canonical_id' => 'id']);
    }
}
