<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon3_x_usage_term".
 *
 * @property integer $id
 * @property string $key
 * @property string $term
 *
 * @property StatWeapon3XUsageRange[] $statWeapon3XUsageRanges
 */
class StatWeapon3XUsageTerm extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_weapon3_x_usage_term';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'term'], 'required'],
            [['term'], 'string'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'term' => 'Term',
        ];
    }

    public function getStatWeapon3XUsageRanges(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsageRange::class, ['term_id' => 'id']);
    }
}
