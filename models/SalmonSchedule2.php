<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\query\SalmonSchedule2Query;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_ASC;

/**
 * This is the model class for table "salmon_schedule2".
 *
 * @property int $id
 * @property int $map_id
 * @property string $start_at
 * @property string $end_at
 *
 * @property SalmonMap2 $map
 * @property SalmonWeapon2[] $weapons
 *
 * @property-read int $period
 */
class SalmonSchedule2 extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new SalmonSchedule2Query(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_schedule2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['map_id', 'start_at', 'end_at'], 'required'],
            [['map_id'], 'default', 'value' => null],
            [['map_id'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'map_id' => 'Map ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap2::class, ['id' => 'map_id']);
    }

    public function getWeapons(): ActiveQuery
    {
        return $this->hasMany(SalmonWeapon2::class, ['schedule_id' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    public function getPeriod(): int
    {
        return BattleHelper::calcPeriod2(strtotime($this->start_at));
    }

    public function delete()
    {
        return Yii::$app->db->transactionEx(function (): bool {
            foreach ($this->weapons as $weapon) {
                if (!$weapon->delete()) {
                    return false;
                }
            }
            return parent::delete();
        });
    }
}
