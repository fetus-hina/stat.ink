<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "medal3".
 *
 * @property integer $id
 * @property string $name
 * @property integer $canonical_id
 *
 * @property BattleMedal3[] $battleMedal3s
 * @property Battle3[] $battles
 * @property MedalCanonical3 $canonical
 */
class Medal3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'medal3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['canonical_id'], 'default', 'value' => null],
            [['name'], 'required'],
            [['canonical_id'], 'default', 'value' => null],
            [['canonical_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['canonical_id'], 'exist', 'skipOnError' => true, 'targetClass' => MedalCanonical3::class, 'targetAttribute' => ['canonical_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'canonical_id' => 'Canonical ID',
        ];
    }

    public function getBattleMedal3s(): ActiveQuery
    {
        return $this->hasMany(BattleMedal3::class, ['medal_id' => 'id']);
    }

    public function getBattles(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['id' => 'battle_id'])->viaTable('battle_medal3', ['medal_id' => 'id']);
    }

    public function getCanonical(): ActiveQuery
    {
        return $this->hasOne(MedalCanonical3::class, ['id' => 'canonical_id']);
    }
}
