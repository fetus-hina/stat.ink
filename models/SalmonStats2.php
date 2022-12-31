<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
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
use yii\helpers\Html;

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
    use openapi\Util;

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
            return $value === false ? null : (int)$value;
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

    public static function openApiSchema(): array
    {
        $nullableBigint = fn (
            string $descriptionEn,
            ?int $minValue = null,
            ?int $maxValue = null
        ): array => array_filter(
            [
                    'type' => 'integer',
                    'format' => 'int64',
                    'minimum' => $minValue,
                    'maximum' => $maxValue,
                    'nullable' => true,
                    'description' => Html::encode(Yii::t('app-apidoc2', $descriptionEn)),
                ],
            fn ($value): bool => $value !== null,
        );

        $timestamp = fn (string $descriptionEn, bool $nullable): array => array_merge(openapi\DateTime::openApiSchema(), [
                'nullable' => $nullable,
                'description' => Html::encode(Yii::t('app-apidoc2', $descriptionEn)),
            ]);

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Salmon Run stats'),
            'properties' => [
                'work_count' => $nullableBigint('Shifts (jobs) worked', 0),
                'total_golden_eggs' => $nullableBigint('Golden Eggs collected', 0),
                'total_eggs' => $nullableBigint('Power Eggs collected', 0),
                'total_rescued' => $nullableBigint('Crew members rescued', 0),
                'total_point' => $nullableBigint('Total points', 0),
                'as_of' => $timestamp('When this data was acquired', true),
                'registered_at' => $timestamp('When this data was sent', false),
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
        ];
    }

    public static function openapiExample(): array
    {
        $ts = fn (string $timestamp): array => DateTimeFormatter::unixTimeToJsonArray(strtotime($timestamp));

        return [
            'work_count' => 388,
            'total_golden_eggs' => 4886,
            'total_eggs' => 177331,
            'total_rescued' => 780,
            'total_point' => 47034,
            'as_of' => $ts('2018-10-04T19:55:44+00:00'),
            'registered_at' => $ts('2018-10-04T19:55:45+00:00'),
        ];
    }
}
