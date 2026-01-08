<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v2;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\models\SalmonStats2;
use app\models\openapi\Util;
use yii\base\Model;
use yii\helpers\Html;

use function array_keys;
use function array_map;
use function array_sum;
use function implode;
use function strtotime;
use function time;

use const SORT_DESC;

class PostSalmonStatsForm extends Model
{
    use Util;

    public const SPLATOON2_4_1_RELEASED_AT = 1538528400;

    public $work_count;
    public $total_golden_eggs;
    public $total_eggs;
    public $total_rescued;
    public $total_point;
    public $as_of;

    public $created_id;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => array_keys($this->attributes),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['as_of'], 'default', 'value' => fn (self $model, string $attribute): int => $_SERVER['REQUEST_TIME'] ?? time()],
            [['work_count', 'total_golden_eggs', 'total_eggs'], 'integer', 'min' => 0],
            [['total_rescued', 'total_point'], 'integer', 'min' => 0],
            [['as_of'], 'integer', 'min' => static::SPLATOON2_4_1_RELEASED_AT],
        ];
    }

    public function findPreviousStats(): ?SalmonStats2
    {
        if (!$this->validate()) {
            return null;
        }

        $model = SalmonStats2::find()
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
        if (!$model) {
            return null;
        }

        $params = [
            'work_count',
            'total_golden_eggs',
            'total_eggs',
            'total_rescued',
            'total_point',
        ];
        $mismatchCount = (int)array_sum(array_map(
            fn (string $param): int => $model->$param != $this->$param ? 1 : 0,
            $params,
        ));
        if ($mismatchCount > 0) {
            return null;
        }

        return $model;
    }

    public function save(): bool
    {
        return Yii::$app->db->transactionEx(function (): bool {
            if (!$this->validate()) {
                return false;
            }

            $model = Yii::createObject(SalmonStats2::class);
            $model->attributes = $this->attributes;
            $model->user_id = Yii::$app->user->identity->id;
            if ($model->save()) {
                $this->created_id = $model->id;
                return true;
            }

            $this->addErrors($model->getErrors());
            return false;
        });
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Salmon Run stats (Grizzco Point Card)'),
            'properties' => [
                'work_count' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Shifts (jobs) worked'),
                ],
                'total_golden_eggs' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Golden Eggs collected'),
                ],
                'total_eggs' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Power Eggs collected'),
                ],
                'total_rescued' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Crew members rescued'),
                ],
                'total_point' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Total points'),
                ],
                'as_of' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'minimum' => static::SPLATOON2_4_1_RELEASED_AT,
                    'description' => implode("\n", [
                        Html::encode(
                            Yii::t('app-apidoc2', 'When this data was acquired in Unix timestamp'),
                        ),
                        '',
                        Html::encode(
                            Yii::t('app-apidoc2', 'Current date time will be used if omitted'),
                        ),
                    ]),
                ],
            ],
            'example' => static::openApiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
        ];
    }

    public static function openApiExample(): array
    {
        return [
            'work_count' => 5436,
            'total_golden_eggs' => 77806,
            'total_eggs' => 3042663,
            'total_rescued' => 13258,
            'total_point' => 966048,
            'as_of' => strtotime('2019-11-08T04:08:05+09:00'),
        ];
    }
}
