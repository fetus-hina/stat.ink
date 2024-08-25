<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_schedule3".
 *
 * @property integer $id
 * @property integer $map_id
 * @property string $start_at
 * @property string $end_at
 * @property integer $big_map_id
 * @property integer $king_id
 * @property boolean $is_eggstra_work
 * @property boolean $is_random_map_big_run
 *
 * @property BigrunMap3 $bigMap
 * @property BigrunOfficialBorder3 $bigrunOfficialBorder3
 * @property BigrunOfficialResult3 $bigrunOfficialResult3
 * @property EggstraWorkOfficialResult3 $eggstraWorkOfficialResult3
 * @property SalmonKing3 $king
 * @property SalmonMap3 $map
 * @property Salmon3[] $salmon3s
 * @property SalmonScheduleWeapon3[] $salmonScheduleWeapon3s
 * @property StatBigrunDistribJobAbstract3 $statBigrunDistribJobAbstract3
 * @property StatBigrunDistribJobHistogram3[] $statBigrunDistribJobHistogram3s
 * @property StatBigrunDistribUserAbstract3 $statBigrunDistribUserAbstract3
 * @property StatBigrunDistribUserHistogram3[] $statBigrunDistribUserHistogram3s
 * @property StatEggstraWorkDistrib3[] $statEggstraWorkDistrib3s
 * @property StatEggstraWorkDistribAbstract3 $statEggstraWorkDistribAbstract3
 * @property StatEggstraWorkDistribUserAbstract3 $statEggstraWorkDistribUserAbstract3
 * @property StatEggstraWorkDistribUserHistogram3[] $statEggstraWorkDistribUserHistogram3s
 * @property UserStatBigrun3[] $userStatBigrun3s
 * @property UserStatEggstraWork3[] $userStatEggstraWork3s
 * @property User[] $users
 * @property User[] $users0
 */
class SalmonSchedule3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_schedule3';
    }

    public function rules()
    {
        return [
            [['map_id', 'big_map_id', 'king_id'], 'default', 'value' => null],
            [['map_id', 'big_map_id', 'king_id'], 'integer'],
            [['start_at', 'end_at'], 'required'],
            [['start_at', 'end_at'], 'safe'],
            [['is_eggstra_work', 'is_random_map_big_run'], 'boolean'],
            [['big_map_id'], 'exist', 'skipOnError' => true, 'targetClass' => BigrunMap3::class, 'targetAttribute' => ['big_map_id' => 'id']],
            [['king_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['king_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'map_id' => 'Map ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'big_map_id' => 'Big Map ID',
            'king_id' => 'King ID',
            'is_eggstra_work' => 'Is Eggstra Work',
            'is_random_map_big_run' => 'Is Random Map Big Run',
        ];
    }

    public function getBigMap(): ActiveQuery
    {
        return $this->hasOne(BigrunMap3::class, ['id' => 'big_map_id']);
    }

    public function getBigrunOfficialBorder3(): ActiveQuery
    {
        return $this->hasOne(BigrunOfficialBorder3::class, ['schedule_id' => 'id']);
    }

    public function getBigrunOfficialResult3(): ActiveQuery
    {
        return $this->hasOne(BigrunOfficialResult3::class, ['schedule_id' => 'id']);
    }

    public function getEggstraWorkOfficialResult3(): ActiveQuery
    {
        return $this->hasOne(EggstraWorkOfficialResult3::class, ['schedule_id' => 'id']);
    }

    public function getKing(): ActiveQuery
    {
        return $this->hasOne(SalmonKing3::class, ['id' => 'king_id']);
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'map_id']);
    }

    public function getSalmon3s(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['schedule_id' => 'id']);
    }

    public function getSalmonScheduleWeapon3s(): ActiveQuery
    {
        return $this->hasMany(SalmonScheduleWeapon3::class, ['schedule_id' => 'id']);
    }

    public function getStatBigrunDistribJobAbstract3(): ActiveQuery
    {
        return $this->hasOne(StatBigrunDistribJobAbstract3::class, ['schedule_id' => 'id']);
    }

    public function getStatBigrunDistribJobHistogram3s(): ActiveQuery
    {
        return $this->hasMany(StatBigrunDistribJobHistogram3::class, ['schedule_id' => 'id']);
    }

    public function getStatBigrunDistribUserAbstract3(): ActiveQuery
    {
        return $this->hasOne(StatBigrunDistribUserAbstract3::class, ['schedule_id' => 'id']);
    }

    public function getStatBigrunDistribUserHistogram3s(): ActiveQuery
    {
        return $this->hasMany(StatBigrunDistribUserHistogram3::class, ['schedule_id' => 'id']);
    }

    public function getStatEggstraWorkDistrib3s(): ActiveQuery
    {
        return $this->hasMany(StatEggstraWorkDistrib3::class, ['schedule_id' => 'id']);
    }

    public function getStatEggstraWorkDistribAbstract3(): ActiveQuery
    {
        return $this->hasOne(StatEggstraWorkDistribAbstract3::class, ['schedule_id' => 'id']);
    }

    public function getStatEggstraWorkDistribUserAbstract3(): ActiveQuery
    {
        return $this->hasOne(StatEggstraWorkDistribUserAbstract3::class, ['schedule_id' => 'id']);
    }

    public function getStatEggstraWorkDistribUserHistogram3s(): ActiveQuery
    {
        return $this->hasMany(StatEggstraWorkDistribUserHistogram3::class, ['schedule_id' => 'id']);
    }

    public function getUserStatBigrun3s(): ActiveQuery
    {
        return $this->hasMany(UserStatBigrun3::class, ['schedule_id' => 'id']);
    }

    public function getUserStatEggstraWork3s(): ActiveQuery
    {
        return $this->hasMany(UserStatEggstraWork3::class, ['schedule_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_stat_bigrun3', ['schedule_id' => 'id']);
    }

    public function getUsers0(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_stat_eggstra_work3', ['schedule_id' => 'id']);
    }
}
