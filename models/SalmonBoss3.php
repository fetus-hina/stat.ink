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
 * This is the model class for table "salmon_boss3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Salmon3[] $salmon
 * @property SalmonBoss3Alias[] $salmonBoss3Aliases
 * @property SalmonBossAppearance3[] $salmonBossAppearance3s
 */
class SalmonBoss3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_boss3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
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
            'name' => 'Name',
        ];
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['id' => 'salmon_id'])->viaTable('salmon_boss_appearance3', ['boss_id' => 'id']);
    }

    public function getSalmonBoss3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss3Alias::class, ['salmonid_id' => 'id']);
    }

    public function getSalmonBossAppearance3s(): ActiveQuery
    {
        return $this->hasMany(SalmonBossAppearance3::class, ['boss_id' => 'id']);
    }
}
