<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_random3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property SalmonScheduleWeapon3[] $salmonScheduleWeapon3s
 */
class SalmonRandom3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_random3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 63],
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

    public function getSalmonScheduleWeapon3s(): ActiveQuery
    {
        return $this->hasMany(SalmonScheduleWeapon3::class, ['random_id' => 'id']);
    }
}
