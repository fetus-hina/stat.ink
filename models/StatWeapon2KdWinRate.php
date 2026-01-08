<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_merge;

/**
 * This is the model class for table "stat_weapon2_kd_win_rate".
 *
 * @property integer $rule_id
 * @property integer $weapon_type_id
 * @property integer $map_id
 * @property integer $version_group_id
 * @property integer $rank_group_id
 * @property integer $kill
 * @property integer $death
 * @property integer $battles
 * @property integer $wins
 *
 * @property Map2 $map
 * @property RankGroup2 $rankGroup
 * @property Rule2 $rule
 * @property SplatoonVersion2 $versionGroup
 * @property Weapon2 $weapon
 */
class StatWeapon2KdWinRate extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function applyFilter(KDWin2FilterForm $form): self
            {
                if ($form->hasErrors()) {
                    return $this->alwaysFalse();
                }

                $this->filterStage($form->map);
                $this->filterRank($form->rank);
                $this->filterWeapon($form->weapon);
                $this->filterVersion($form->version);

                return $this;
            }

            public function alwaysFalse(): self
            {
                $this->andWhere('0 = 1');
                return $this;
            }

            public function filterStage(?string $key): self
            {
                if ($key == '') {
                    return $this;
                }

                if ($key === 'mystery') {
                    $this->andWhere([
                        'map_id' => ArrayHelper::getColumn(
                            Map2::find()
                                ->andWhere(['like', 'key', 'mystery%', false])
                                ->asArray()
                                ->all(),
                            'id',
                        ),
                    ]);
                } else {
                    $model = Map2::findOne(['key' => $key]);
                    if (!$model) {
                        return $this->alwaysFalse();
                    }
                    $this->andWhere(['map_id' => $model->id]);
                }

                return $this;
            }

            public function filterRank(?string $key): self
            {
                if ($key == '') {
                    return $this;
                }

                $model = RankGroup2::findOne(['key' => $key]);
                if (!$model) {
                    return $this->alwaysFalse();
                }
                $this->andWhere(['rank_group_id' => $model->id]);

                $model = Rule2::findOne(['key' => 'nawabari']);
                if ($model) {
                    $this->andWhere(['<>', 'rule_id', $model->id]);
                }

                return $this;
            }

            public function filterWeapon(?string $key): self
            {
                if ($key == '') {
                    return $this;
                }

                $model = WeaponType2::findOne(['key' => $key]);
                if (!$model) {
                    return $this->alwaysFalse();
                }
                $this->andWhere(['weapon_type_id' => $model->id]);

                return $this;
            }

            public function filterVersion(?string $vstr): self
            {
                if ($vstr == '' || $vstr === '*') {
                    return $this;
                }

                $v = SplatoonVersionGroup2::findOne(['tag' => $vstr]);
                if (!$v) {
                    return $this->alwaysFalse();
                }

                $this->andWhere(['version_group_id' => $v->id]);
                return $this;
            }
        };
    }

    public static function tableName()
    {
        return 'stat_weapon2_kd_win_rate';
    }

    public function rules()
    {
        $pKeyGroup = [
            'rule_id',
            'weapon_type_id',
            'map_id',
            'version_group_id',
            'rank_group_id',
            'kill',
            'death',
        ];
        return [
            [array_merge($pKeyGroup, ['battles', 'wins']), 'required'],
            [array_merge($pKeyGroup, ['battles', 'wins']), 'default', 'value' => null],
            [array_merge($pKeyGroup, ['battles', 'wins']), 'integer'],
            [$pKeyGroup, 'unique', 'targetAttribute' => $pKeyGroup],
            [['map_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
            [['rank_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => RankGroup2::class,
                'targetAttribute' => ['rank_group_id' => 'id'],
            ],
            [['rule_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['version_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['version_group_id' => 'id'],
            ],
            [['weapon_type_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => WeaponType2::class,
                'targetAttribute' => ['weapon_type_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'weapon_type_id' => 'Weapon Type ID',
            'map_id' => 'Map ID',
            'version_group_id' => 'Version Group ID',
            'rank_group_id' => 'Rank Group ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    public function getRankGroup(): ActiveQuery
    {
        return $this->hasOne(RankGroup2::class, ['id' => 'rank_group_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getVersionGroup(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_group_id']);
    }

    public function getWeaponType(): ActiveQuery
    {
        return $this->hasOne(WeaponType2::class, ['id' => 'weapon_type_id']);
    }
}
