<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function sprintf;

use const SORT_DESC;

/**
 * This is the model class for table "rank".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $int_base
 *
 * @property Battle[] $battles
 * @property RankGroup $group
 */
final class Rank extends ActiveRecord
{
    use SafeFindOneTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'int_base'], 'required'],
            [['key', 'name'], 'string', 'max' => 16],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['int_base'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['rank_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(RankGroup::class, ['id' => 'group_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'zone' => $this->group->toJsonArray(),
            'name' => Translator::translateToAll('app-rank', $this->name),
        ];
    }

    public static function integerToString(?int $intRank): ?string
    {
        if ($intRank === null) {
            return null;
        }

        $rank = static::find()
            ->andWhere(['<=', '{{rank}}.[[int_base]]', $intRank])
            ->orderBy(['int_base' => SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();

        if (!$rank) {
            return null;
        }

        return sprintf(
            '%s %d',
            Yii::t('app-rank', $rank['name']),
            $intRank - $rank['int_base'],
        );
    }
}
