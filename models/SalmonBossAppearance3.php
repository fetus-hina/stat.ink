<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
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
 * This is the model class for table "salmon_boss_appearance3".
 *
 * @property integer $salmon_id
 * @property integer $boss_id
 * @property integer $appearances
 * @property integer $defeated
 * @property integer $defeated_by_me
 *
 * @property SalmonBoss3 $boss
 * @property Salmon3 $salmon
 */
class SalmonBossAppearance3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_boss_appearance3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['salmon_id', 'boss_id', 'appearances', 'defeated', 'defeated_by_me'], 'required'],
            [['salmon_id', 'boss_id', 'appearances', 'defeated', 'defeated_by_me'], 'default', 'value' => null],
            [['salmon_id', 'boss_id', 'appearances', 'defeated', 'defeated_by_me'], 'integer'],
            [['salmon_id', 'boss_id'], 'unique', 'targetAttribute' => ['salmon_id', 'boss_id']],
            [['salmon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salmon3::class, 'targetAttribute' => ['salmon_id' => 'id']],
            [['boss_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonBoss3::class, 'targetAttribute' => ['boss_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'salmon_id' => 'Salmon ID',
            'boss_id' => 'Boss ID',
            'appearances' => 'Appearances',
            'defeated' => 'Defeated',
            'defeated_by_me' => 'Defeated By Me',
        ];
    }

    public function getBoss(): ActiveQuery
    {
        return $this->hasOne(SalmonBoss3::class, ['id' => 'boss_id']);
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasOne(Salmon3::class, ['id' => 'salmon_id']);
    }
}
