<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

namespace app\models\api\v2;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\models\SalmonStats2;
use yii\base\Model;

class PostSalmonStatsForm extends Model
{
    const SPLATOON2_4_1_RELEASED_AT = 1538528400;

    public $work_count;
    public $total_golden_eggs;
    public $total_eggs;
    public $total_rescued;
    public $total_point;
    public $as_of;

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
            [['as_of'], 'default', 'value' => function (self $model, string $attribute): int {
                return $_SERVER['REQUEST_TIME'] ?? time();
            }],
            [['work_count', 'total_golden_eggs', 'total_eggs'], 'integer', 'min' => 0],
            [['total_rescued', 'total_point'], 'integer', 'min' => 0],
            [['as_of'], 'integer', 'min' => static::SPLATOON2_4_1_RELEASED_AT],
        ];
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
                return true;
            }

            $this->addErrors($model->getErrors());
            return false;
        });
    }
}
