<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "species3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattleTricolorPlayer3[] $battleTricolorPlayer3s
 * @property SalmonPlayer3[] $salmonPlayer3s
 */
class Species3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'species3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['species_id' => 'id']);
    }

    public function getBattleTricolorPlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattleTricolorPlayer3::class, ['species_id' => 'id']);
    }

    public function getSalmonPlayer3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer3::class, ['species_id' => 'id']);
    }
}
