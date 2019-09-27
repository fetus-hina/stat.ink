<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Salmon2ClearStats extends Model
{
    public $stage;
    public $stage_id;
    public $jobs;
    public $w1_failed;
    public $w2_failed;
    public $w3_failed;
    public $cleared;

    public static function all(): array
    {
        $stages = ArrayHelper::map(
            SalmonMap2::find()->all(),
            'id',
            function (SalmonMap2 $obj): SalmonMap2 {
                return $obj;
            }
        );
        $query = (new Query())
            ->select([
                'stage_id' => 'stage_id',
                'jobs' => 'COUNT(*)',
                'w1_failed' => 'SUM(CASE WHEN clear_waves = 0 THEN 1 ELSE 0 END)',
                'w2_failed' => 'SUM(CASE WHEN clear_waves = 1 THEN 1 ELSE 0 END)',
                'w3_failed' => 'SUM(CASE WHEN clear_waves = 2 THEN 1 ELSE 0 END)',
                'cleared' => 'SUM(CASE WHEN clear_waves > 2  THEN 1 ELSE 0 END)',
            ])
            ->from('salmon2')
            ->andWhere(['and',
                ['not', ['clear_waves' => null]],
                ['not', ['stage_id' => null]],
                ['is_automated' => true],
            ])
            ->orderBy([
                'stage_id' => SORT_ASC,
            ])
            ->groupBy(['stage_id']);
        //TODO: filter
        return array_map(
            function (array $row) use ($stages, $query): self {
                return Yii::createObject(array_merge($row, [
                    'class' => static::class,
                    'stage' => $stages[$row['stage_id']],
                ]));
            },
            $query->all()
        );
    }
}
