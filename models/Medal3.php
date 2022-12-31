<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "medal3".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BattleMedal3[] $battleMedal3s
 * @property Battle3[] $battles
 */
class Medal3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'medal3';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getBattleMedal3s(): ActiveQuery
    {
        return $this->hasMany(BattleMedal3::class, ['medal_id' => 'id']);
    }

    public function getBattles(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['id' => 'battle_id'])->viaTable('battle_medal3', ['medal_id' => 'id']);
    }
}
