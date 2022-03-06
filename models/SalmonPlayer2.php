<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_ASC;
use const SORT_DESC;

/**
 * This is the model class for table "salmon_player2".
 *
 * @property int $id
 * @property int $work_id
 * @property bool $is_me
 * @property string $splatnet_id
 * @property string $name
 * @property int $special_id
 * @property int $rescue
 * @property int $death
 * @property int $golden_egg_delivered
 * @property int $power_egg_collected
 * @property int $species_id
 * @property int $gender_id
 *
 * @property SalmonBoss2[] $bosses
 * @property Gender $gender
 * @property SalmonPlayerBossKill2[] $salmonPlayerBossKill2s
 * @property SalmonPlayerSpecialUse2[] $salmonPlayerSpecialUse2s
 * @property SalmonPlayerWeapon2[] $salmonPlayerWeapon2s
 * @property SalmonSpecial2 $special
 * @property Species2 $species
 * @property Salmon2 $work
 *
 * @property-read ForceBlackout2 $forceBlackout
 * @property-read SalmonPlayerBossKill2[] $bossKills
 * @property-read SalmonPlayerSpecialUse2 $specialUses
 * @property-read SalmonPlayerWeapon2[] $weapons
 * @property-read bool $isForceBlackout
 */
class SalmonPlayer2 extends ActiveRecord
{
    use openapi\Util;

    public $top_500 = false; // compat with BattlePlayer2 and used by PlayerName2Widget
    private $user = false;

    public static function tableName()
    {
        return 'salmon_player2';
    }

    public function rules()
    {
        return [
            [['work_id', 'is_me'], 'required'],
            [['work_id', 'special_id', 'rescue', 'death'], 'default', 'value' => null],
            [['golden_egg_delivered', 'power_egg_collected', 'species_id', 'gender_id'], 'default',
                'value' => null,
            ],
            [['work_id', 'special_id', 'rescue', 'death'], 'integer'],
            [['golden_egg_delivered', 'power_egg_collected', 'species_id', 'gender_id'], 'integer'],
            [['is_me'], 'boolean'],
            [['splatnet_id'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 10],
            [['gender_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Gender::class,
                'targetAttribute' => ['gender_id' => 'id'],
            ],
            [['work_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Salmon2::class,
                'targetAttribute' => ['work_id' => 'id'],
            ],
            [['special_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => ['special_id' => 'id'],
            ],
            [['species_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Species2::class,
                'targetAttribute' => ['species_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_id' => 'Work ID',
            'is_me' => 'Is Me',
            'splatnet_id' => 'Splatnet ID',
            'name' => 'Name',
            'special_id' => 'Special ID',
            'rescue' => 'Rescue',
            'death' => 'Death',
            'golden_egg_delivered' => 'Golden Eggs Delivered',
            'power_egg_collected' => 'Power Eggs Collected',
            'species_id' => 'Species ID',
            'gender_id' => 'Gender ID',
        ];
    }

    public function getGender(): ActiveQuery
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    public function getWork(): ActiveQuery
    {
        return $this->hasOne(Salmon2::class, ['id' => 'work_id']);
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(SalmonSpecial2::class, ['id' => 'special_id']);
    }

    public function getSpecies(): ActiveQuery
    {
        return $this->hasOne(Species2::class, ['id' => 'species_id']);
    }

    public function getBossKills(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayerBossKill2::class, ['player_id' => 'id'])
            ->with('boss');
    }

    public function getSalmonPlayerBossKill2s(): ActiveQuery
    {
        return $this->getBossKills();
    }

    public function getBosses(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss2::class, ['id' => 'boss_id'])
            ->viaTable('salmon_player_boss_kill2', ['player_id' => 'id']);
    }

    public function getSpecialUses(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayerSpecialUse2::class, ['player_id' => 'id'])
            ->orderBy([
                'salmon_player_special_use2.player_id' => SORT_ASC,
                'salmon_player_special_use2.wave' => SORT_ASC,
            ]);
    }

    public function getSalmonPlayerSpecialUse2s(): ActiveQuery
    {
        return $this->getSpecialUses();
    }

    public function getWeapons(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayerWeapon2::class, ['player_id' => 'id'])
            ->with(['weapon'])
            ->orderBy([
                'salmon_player_weapon2.player_id' => SORT_ASC,
                'salmon_player_weapon2.wave' => SORT_ASC,
            ]);
    }

    public function getSalmonPlayerWeapon2s(): ActiveQuery
    {
        return $this->getWeapons();
    }

    public function getJdenticonHash(): string
    {
        $id = $this->getAnonymizeSeed();
        if (preg_match('/^([0-9a-f]{2}+)[0-9a-f]?$/', $id, $match)) {
            $id = hex2bin($match[1]);
        }
        return substr(
            hash('sha256', $id, false),
            0,
            40
        );
    }

    public function getAnonymizeSeed(): string
    {
        $value = trim($this->splatnet_id);
        return $value !== ''
            ? $value
            : hash_hmac('sha256', (string)$this->id, (string)$this->work_id);
    }

    public function getIconUrl(string $ext = 'svg'): string
    {
        if ($user = $this->getUser()) {
            return $user->getIconUrl($ext);
        }
        $hash = $this->getJdenticonHash();
        return Yii::getAlias('@jdenticon') . '/' . $hash . '.' . $ext;
    }

    public function getForceBlackout()
    {
        return $this->hasOne(ForceBlackout2::class, ['splatnet_id' => 'splatnet_id']);
    }

    public function getIsForceBlackouted(): bool
    {
        return $this->forceBlackout !== null;
    }

    public function getUser(): ?User
    {
        if ($this->user === false) {
            $id = trim((string)$this->splatnet_id);
            if ($id === '') {
                $this->user = null;
            } else {
                $model = Splatnet2UserMap::find()
                    ->with('user')
                    ->andWhere(['splatnet_id' => $id])
                    ->orderBy(['battles' => SORT_DESC])
                    ->limit(1)
                    ->one();
                $this->user = $model->user ?? null;
            }
        }
        return $this->user;
    }

    public function toJsonArray(): array
    {
        if ($this->is_me) {
            $anonymize = false;
        } elseif ($this->getIsForceBlackouted()) {
            $anonymize = true;
        } else {
            $anonymize = false;
        }

        return [
            'splatnet_id' => $this->splatnet_id,
            'name' => $anonymize ? str_repeat('*', 10) : $this->name,
            'special' => $this->special_id
                ? $this->special->toJsonArray()
                : null,
            'rescue' => $this->rescue,
            'death' => $this->death,
            'golden_egg_delivered' => $this->golden_egg_delivered,
            'power_egg_collected' => $this->power_egg_collected,
            'species' => $this->species_id
                ? $this->species->toJsonArray()
                : null,
            'gender' => $this->gender_id
                ? $this->gender->toJsonArray()
                : null,
            'special_uses' => $this->specialUses
                ? array_map(
                    fn ($model) => (int)$model->count,
                    $this->specialUses
                )
                : null,
            'weapons' => $this->weapons
                ? array_map(
                    fn ($model) => $model->weapon ? $model->weapon->toJsonArray() : null,
                    $this->weapons
                )
                : null,
            'boss_kills' => $this->bossKills
                ? array_map(
                    fn ($model) => $model->toJsonArray(),
                    $this->bossKills
                )
                : null,
        ];
    }

    public function delete()
    {
        return Yii::$app->db->transactionEx(function (): bool {
            $profile = "Delete salmon_player2 (id={$this->id})";
            Yii::beginProfile($profile, __METHOD__);

            foreach ($this->bossKills as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            foreach ($this->specialUses as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            foreach ($this->weapons as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            $result = !!parent::delete();
            Yii::endProfile($profile, __METHOD__);
            return $result;
        });
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Player results'),
            'properties' => [
                'splatnet_id' => [
                    'type' => 'string',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Unique ID of the player'),
                ],
                'name' => [
                    'type' => 'string',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Player name'),
                ],
                'special' => array_merge(Special2::openApiSchema(), [
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'What special weapon assigned'),
                ]),
                'rescue' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Number of times rescued other players'),
                ],
                'death' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t(
                        'app-apidoc2',
                        'Number of times rescued by other players'
                    ),
                ],
                'golden_egg_delivered' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Golden Eggs delivered'),
                ],
                'power_egg_collected' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Power Eggs collected'),
                ],
                'species' => array_merge(Species2::openApiSchema(), [
                    'nullable' => true,
                ]),
                'gender' => array_merge(Gender::openApiSchema(), [
                    'nullable' => true,
                ]),
                'special_uses' => [
                    'type' => 'array',
                    'nullable' => true,
                    'description' => Yii::t(
                        'app-apidoc2',
                        'How many times special weapon used in each wave'
                    ),
                    'items' => [
                        'type' => 'integer',
                        'format' => 'int32',
                        'minimum' => 0,
                        'maximum' => 2,
                    ],
                    'minItems' => 1,
                    'maxItems' => 3,
                ],
                'weapons' => [
                    'type' => 'array',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Weapons loaned in each wave'),
                    'items' => array_merge(SalmonMainWeapon2::openApiSchema(), [
                        'nullable' => true,
                    ]),
                    'minItems' => 1,
                    'maxItems' => 3,
                ],
                'boss_kills' => [
                    'type' => 'array',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Number of kills the boss salmonid'),
                    'items' => static::oapiRef(SalmonPlayerBossKill2::class),
                ],
            ],
            'example' => [],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            SalmonPlayerBossKill2::class,
        ];
    }

    public static function openApiExample(): array
    {
        return [];
    }
}
