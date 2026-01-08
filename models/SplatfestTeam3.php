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
 * This is the model class for table "splatfest_team3".
 *
 * @property integer $id
 * @property integer $fest_id
 * @property integer $camp_id
 * @property string $name
 * @property string $color
 *
 * @property SplatfestCamp3 $camp
 * @property Splatfest3 $fest
 */
class SplatfestTeam3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest_team3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['fest_id', 'camp_id', 'name', 'color'], 'required'],
            [['fest_id', 'camp_id'], 'default', 'value' => null],
            [['fest_id', 'camp_id'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['color'], 'string', 'max' => 8],
            [['fest_id', 'camp_id'], 'unique', 'targetAttribute' => ['fest_id', 'camp_id']],
            [['fest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3::class, 'targetAttribute' => ['fest_id' => 'id']],
            [['camp_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatfestCamp3::class, 'targetAttribute' => ['camp_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fest_id' => 'Fest ID',
            'camp_id' => 'Camp ID',
            'name' => 'Name',
            'color' => 'Color',
        ];
    }

    public function getCamp(): ActiveQuery
    {
        return $this->hasOne(SplatfestCamp3::class, ['id' => 'camp_id']);
    }

    public function getFest(): ActiveQuery
    {
        return $this->hasOne(Splatfest3::class, ['id' => 'fest_id']);
    }
}
