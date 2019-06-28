<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

namespace app\models;

use DateTime;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\DateTimeFormatter;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_stats2".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $work_count
 * @property integer $total_golden_eggs
 * @property integer $total_eggs
 * @property integer $total_rescued
 * @property integer $total_point
 * @property string $as_of
 * @property string $created_at
 *
 * @property User $user
 */
class SalmonStats2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_stats2';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'as_of'], 'required'],
            [['as_of'], 'filter', 'filter' => function ($value) {
                if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                    return gmdate(DateTime::ATOM, (int)$value);
                }

                return $value;
            }],
            [['user_id'], 'integer'],
            [['work_count', 'total_golden_eggs', 'total_eggs'], 'integer', 'min' => 0],
            [['total_rescued', 'total_point'], 'integer', 'min' => 0],
            [['as_of', 'created_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'work_count' => 'Work Count',
            'total_golden_eggs' => 'Total Golden Eggs',
            'total_eggs' => 'Total Eggs',
            'total_rescued' => 'Total Rescued',
            'total_point' => 'Total Point',
            'as_of' => 'As Of',
            'created_at' => 'Created At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function toJsonArray(): array
    {
        $int = function ($value): ?int {
            if ($value === null || $value === '') {
                return null;
            }
            $value = filter_var($value, FILTER_VALIDATE_INT);
            return ($value === false) ? null : (int)$value;
        };

        $timestamp = function ($value): ?array {
            if ($value === null || $value === '') {
                return null;
            }
            $value = strtotime((string)$value);
            if ($value === false) {
                return null;
            }
            return DateTimeFormatter::unixTimeToJsonArray($value);
        };

        return [
            'work_count' => $int($this->work_count),
            'total_golden_eggs' => $int($this->total_golden_eggs),
            'total_eggs' => $int($this->total_eggs),
            'total_rescued' => $int($this->total_rescued),
            'total_point' => $int($this->total_point),
            'as_of' => $timestamp($this->as_of),
            'registered_at' => $timestamp($this->created_at),
        ];
    }
}
