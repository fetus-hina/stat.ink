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
 * This is the model class for table "salmon_player3".
 *
 * @property integer $id
 * @property integer $salmon_id
 * @property boolean $is_me
 * @property string $name
 * @property string $number
 * @property integer $splashtag_title_id
 * @property integer $uniform_id
 * @property integer $special_id
 * @property integer $golden_eggs
 * @property integer $golden_assist
 * @property integer $power_eggs
 * @property integer $rescue
 * @property integer $rescued
 * @property integer $defeat_boss
 * @property boolean $is_disconnected
 * @property integer $species_id
 *
 * @property Salmon3 $salmon
 * @property SalmonPlayerWeapon3[] $salmonPlayerWeapon3s
 * @property Special3 $special
 * @property Species3 $species
 * @property SplashtagTitle3 $splashtagTitle
 * @property SalmonUniform3 $uniform
 */
class SalmonPlayer3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_player3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['name', 'number', 'splashtag_title_id', 'uniform_id', 'special_id', 'golden_eggs', 'golden_assist', 'power_eggs', 'rescue', 'rescued', 'defeat_boss', 'species_id'], 'default', 'value' => null],
            [['salmon_id', 'is_me', 'is_disconnected'], 'required'],
            [['salmon_id', 'splashtag_title_id', 'uniform_id', 'special_id', 'golden_eggs', 'golden_assist', 'power_eggs', 'rescue', 'rescued', 'defeat_boss', 'species_id'], 'default', 'value' => null],
            [['salmon_id', 'splashtag_title_id', 'uniform_id', 'special_id', 'golden_eggs', 'golden_assist', 'power_eggs', 'rescue', 'rescued', 'defeat_boss', 'species_id'], 'integer'],
            [['is_me', 'is_disconnected'], 'boolean'],
            [['name'], 'string', 'max' => 10],
            [['number'], 'string', 'max' => 32],
            [['salmon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salmon3::class, 'targetAttribute' => ['salmon_id' => 'id']],
            [['uniform_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonUniform3::class, 'targetAttribute' => ['uniform_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
            [['species_id'], 'exist', 'skipOnError' => true, 'targetClass' => Species3::class, 'targetAttribute' => ['species_id' => 'id']],
            [['splashtag_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplashtagTitle3::class, 'targetAttribute' => ['splashtag_title_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'salmon_id' => 'Salmon ID',
            'is_me' => 'Is Me',
            'name' => 'Name',
            'number' => 'Number',
            'splashtag_title_id' => 'Splashtag Title ID',
            'uniform_id' => 'Uniform ID',
            'special_id' => 'Special ID',
            'golden_eggs' => 'Golden Eggs',
            'golden_assist' => 'Golden Assist',
            'power_eggs' => 'Power Eggs',
            'rescue' => 'Rescue',
            'rescued' => 'Rescued',
            'defeat_boss' => 'Defeat Boss',
            'is_disconnected' => 'Is Disconnected',
            'species_id' => 'Species ID',
        ];
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasOne(Salmon3::class, ['id' => 'salmon_id']);
    }

    public function getSalmonPlayerWeapon3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayerWeapon3::class, ['player_id' => 'id']);
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getSpecies(): ActiveQuery
    {
        return $this->hasOne(Species3::class, ['id' => 'species_id']);
    }

    public function getSplashtagTitle(): ActiveQuery
    {
        return $this->hasOne(SplashtagTitle3::class, ['id' => 'splashtag_title_id']);
    }

    public function getUniform(): ActiveQuery
    {
        return $this->hasOne(SalmonUniform3::class, ['id' => 'uniform_id']);
    }
}
