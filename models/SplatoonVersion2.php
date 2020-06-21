<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatoon_version2".
 *
 * @property integer $id
 * @property string $tag
 * @property string $name
 * @property string $released_at
 * @property integer $group_id
 *
 * @property Battle2[] $battles
 * @property SplatoonVersionGroup2 $group
 * @property StatWeapon2Result[] $statWeapon2Results
 */
class SplatoonVersion2 extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function __construct(string $modelClass, array $config = [])
            {
                parent::__construct($modelClass, $config);

                list(, $t) = $this->getTableNameAndAlias();
                $this->orderBy([
                    "{{{$t}}}.[[released_at]]" => SORT_DESC,
                ]);
            }

            public function released(?DateTimeInterface $at = null): self
            {
                if (!$at) {
                    $at = (new DateTimeImmutable())
                        ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
                        ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
                }

                list(, $t) = $this->getTableNameAndAlias();
                $this->andWhere(['<=', "{{{$t}}}.[[released_at]]", $at->format(DateTime::ATOM)]);
                return $this;
            }
        };
    }

    public static function findCurrentVersion($at = null): ?self
    {
        if ($at !== null && !($at instanceof DateTimeInterface)) {
            $ts = Yii::$app->formatter->asTimestamp($at);
            $at = (new DateTimeImmutable())
                ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
                ->setTimestamp((int)$ts);
        }

        return static::find()
            ->released($at)
            ->limit(1)
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatoon_version2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag', 'name', 'released_at', 'group_id'], 'required'],
            [['released_at'], 'safe'],
            [['group_id'], 'default', 'value' => null],
            [['group_id'], 'integer'],
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['tag'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersionGroup2::class,
                'targetAttribute' => ['group_id' => 'id'],
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
            'tag' => 'Tag',
            'name' => 'Name',
            'released_at' => 'Released At',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle2::class, ['version_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(SplatoonVersionGroup2::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeapon2Results()
    {
        return $this->hasMany(StatWeapon2Result::class, ['version_id' => 'id']);
    }
}
