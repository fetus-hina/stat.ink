<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v2\salmon;

use Yii;
use app\components\behaviors\AutoTrimAttributesBehavior;
use app\components\helpers\ApiInputFormatter;
use app\models\Gender;
use app\models\Salmon2;
use app\models\SalmonBoss2;
use app\models\SalmonMainWeapon2;
use app\models\SalmonPlayer2;
use app\models\SalmonPlayerBossKill2;
use app\models\SalmonPlayerSpecialUse2;
use app\models\SalmonPlayerWeapon2;
use app\models\SalmonSpecial2;
use app\models\Species2;
use app\models\openapi\SplatNet2PrincipalID;
use app\models\openapi\Util as OpenAPIUtil;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\validators\NumberValidator;

use function count;
use function implode;
use function is_array;
use function sprintf;
use function strtolower;

use const SORT_ASC;

class Player extends Model
{
    use OpenAPIUtil;

    public $is_me;
    public $splatnet_id;
    public $name;
    public $special;
    public $rescue;
    public $death;
    public $golden_egg_delivered;
    public $power_egg_collected;
    public $species;
    public $gender;
    public $special_uses;
    public $weapons;
    public $boss_kills;

    public function behaviors()
    {
        return [
            AutoTrimAttributesBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['is_me'], 'required'],
            [['splatnet_id'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 10],
            [['special', 'species', 'gender'], 'string'],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['rescue', 'death', 'golden_egg_delivered', 'power_egg_collected'], 'integer', 'min' => 0],
            [['species'], 'in', 'range' => ['inkling', 'octoling']],
            [['gender'], 'in', 'range' => ['boy', 'girl']],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => ['special' => 'key'],
            ],
            [['special_uses'], 'validateSpecialUses'],
            [['weapons'], 'validateWeapons'],
            [['boss_kills'], 'validateBossKills'],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function validateSpecialUses(): void
    {
        // {{{
        if ($this->hasErrors('special_uses')) {
            return;
        }

        if ($this->special_uses === null || $this->special_uses === '') {
            $this->special_uses = null;
            return;
        }

        if (!is_array($this->special_uses)) {
            $this->addError('special_uses', 'special_uses should be an array');
            return;
        }

        if (!ArrayHelper::isIndexed($this->special_uses)) {
            $this->addError('special_uses', 'special_uses should be an array (not associative array)');
            return;
        }

        if (count($this->special_uses) > 3) {
            $this->addError('special_uses', 'too many special_uses');
            return;
        }

        $countValidator = Yii::createObject([
            'class' => NumberValidator::class,
            'integerOnly' => true,
            'min' => 0,
            'max' => 3,
        ]);
        for ($i = 0; $i < 3; ++$i) {
            $value = $this->special_uses[$i] ?? null;
            if ($value === null) {
                break;
            }

            $error = null;
            if (!$countValidator->validate($value, $error)) {
                $this->addError('special_uses', sprintf('%d: %s', $i, $error));
                continue;
            }
        }
        // }}}
    }

    public function validateWeapons(): void
    {
        // {{{
        if ($this->hasErrors('weapons')) {
            return;
        }

        if ($this->weapons === null || $this->weapons === '') {
            $this->weapons = null;
            return;
        }

        if (!is_array($this->weapons)) {
            $this->addError('weapons', 'weapons should be an array');
            return;
        }

        if (empty($this->weapons)) {
            $this->weapons = null;
            return;
        }

        if (count($this->weapons) > 3) {
            $this->addError('weapons', 'too many weapons');
            return;
        }

        for ($i = 0; $i < 3; ++$i) {
            $value = $this->weapons[$i] ?? null;
            if ($value === '' || $value === null) {
                break;
            }

            $model = SalmonMainWeapon2::findOne(['key' => $value]);
            if (!$model) {
                $this->addError('weapons', sprintf('unknown key "%s"', (string)$value));
                continue;
            }
        }
        // }}}
    }

    public function validateBossKills(): void
    {
        // {{{
        if ($this->hasErrors('boss_kills')) {
            return;
        }

        if ($this->boss_kills === null || $this->boss_kills === '') {
            $this->boss_kills = null;
            return;
        }

        if (!is_array($this->boss_kills)) {
            $this->addError('boss_kills', 'boss_kills should be an associative array');
            return;
        }

        if (empty($this->boss_kills)) {
            $this->boss_kills = null;
            return;
        }

        $countValidator = Yii::createObject([
            'class' => NumberValidator::class,
            'integerOnly' => true,
            'min' => 0,
        ]);
        foreach ($this->boss_kills as $key => $value) {
            $boss = SalmonBoss2::findOne(['key' => (string)$key]);
            if (!$boss) {
                $this->addError('boss_kills', sprintf('unknown key "%s"', (string)$key));
                continue;
            }

            $error = null;
            if (!$countValidator->validate($value, $error)) {
                $this->addError('boss_kills', sprintf('%s: %s', $key, $error));
                continue;
            }
        }
        // }}}
    }

    public function save(Salmon2 $work): bool
    {
        return Yii::$app->db->transactionEx(function () use ($work): bool {
            if (!$this->validate()) {
                return false;
            }

            if (!$player = $this->savePlayer($work)) {
                return false;
            }

            return $this->saveSpecialUses($player) &&
                $this->saveWeapons($player) &&
                $this->saveBossKills($player);
        });
    }

    protected function savePlayer(Salmon2 $work): ?SalmonPlayer2
    {
        return Yii::$app->db->transactionEx(function () use ($work): ?SalmonPlayer2 {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            $model = Yii::createObject([
                'class' => SalmonPlayer2::class,
                'work_id' => $work->id,
                'is_me' => $this->is_me === 'yes',
                'splatnet_id' => $fmt->asString($this->splatnet_id),
                'name' => $fmt->asString($this->name),
                'special_id' => $fmt->asKeyId($this->special, SalmonSpecial2::class, 'key', 'splatnet'),
                'rescue' => $fmt->asInteger($this->rescue),
                'death' => $fmt->asInteger($this->death),
                'golden_egg_delivered' => $fmt->asInteger($this->golden_egg_delivered),
                'power_egg_collected' => $fmt->asInteger($this->power_egg_collected),
                'species_id' => $fmt->asKeyId($this->species, Species2::class),
                'gender_id' => (function () use ($fmt): ?int {
                    switch ($fmt->asString($this->gender)) {
                        case 'boy':
                            return 1;

                        case 'girl':
                            return 2;

                        default:
                            return null;
                    }
                })(),
            ]);
            return $model->save() ? $model : null;
        });
    }

    protected function saveSpecialUses(SalmonPlayer2 $player): bool
    {
        if (!$this->special_uses) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            for ($i = 0; $i < 3; ++$i) {
                $data = $this->special_uses[$i] ?? null;
                if ($data === null) {
                    break;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerSpecialUse2::class,
                    'player_id' => $player->id,
                    'wave' => $i + 1,
                    'count' => $fmt->asInteger($data),
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function saveWeapons(SalmonPlayer2 $player): bool
    {
        if (!$this->weapons) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            for ($i = 0; $i < 3; ++$i) {
                $data = $this->weapons[$i] ?? null;
                if ($data === null) {
                    break;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerWeapon2::class,
                    'player_id' => $player->id,
                    'wave' => $i + 1,
                    'weapon_id' => $fmt->asKeyId($data, SalmonMainWeapon2::class, 'key', 'splatnet'),
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function saveBossKills(SalmonPlayer2 $player): bool
    {
        if (!$this->boss_kills) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            foreach ($this->boss_kills as $bossKey => $count) {
                if ($count < 1) {
                    continue;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerBossKill2::class,
                    'player_id' => $player->id,
                    'boss_id' => $fmt->asKeyId($bossKey, SalmonBoss2::class),
                    'count' => (int)$count,
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Player\'s data'),
            'properties' => [
                'splatnet_id' => static::oapiRef(SplatNet2PrincipalID::class),
                'name' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 10,
                    'description' => Yii::t('app-apidoc2', 'Player\'s in-game name'),
                ],
                'special' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'What special weapon assigned')),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Special weapon'),
                            'app-special2',
                            SalmonSpecial2::find()
                                ->orderBy(['key' => SORT_ASC])
                                ->all(),
                            null,
                            null,
                            null,
                            ['splatnet'],
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        SalmonSpecial2::find()
                            ->orderBy(['key' => SORT_ASC])
                            ->all(),
                        'key',
                        false,
                    ),
                    true, // replace description
                ),
                'rescue' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Number of times rescued other players'),
                ],
                'death' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t(
                        'app-apidoc2',
                        'Number of times rescued by other players',
                    ),
                ],
                'golden_egg_delivered' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Golden Eggs delivered'),
                ],
                'power_egg_collected' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'Power Eggs collected'),
                ],
                'species' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Species')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'If your client doesn\'t/cannot detect this data, omit this field or ' .
                            'send just `null`.',
                        )),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Species'),
                            'app',
                            Species2::find()
                                ->orderBy(['id' => SORT_ASC])
                                ->asArray()
                                ->all(),
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        Species2::find()
                            ->orderBy(['id' => SORT_ASC])
                            ->asArray()
                            ->all(),
                        'key',
                        false,
                    ),
                    true, // replace description
                ),
                'gender' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Gender')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'If your client doesn\'t/cannot detect this data, omit this field or ' .
                            'send just `null`.',
                        )),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Gender'),
                            'app',
                            Gender::find()
                                ->orderBy(['id' => SORT_ASC])
                                ->all(),
                            fn (Gender $model): string => strtolower($model->name),
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        Gender::find()
                            ->orderBy(['id' => SORT_ASC])
                            ->all(),
                        fn (Gender $model): string => strtolower($model->name),
                        false,
                    ),
                    true, // replace description
                ),
                'special_uses' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 3,
                    'description' => implode("\n", [
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'How many times the special weapon was used in each wave',
                        )),
                        '',
                        '```js',
                        '{',
                        '  "special_uses": [',
                        '    0, // WAVE 1',
                        '    1, // WAVE 2',
                        '    1  // WAVE 3',
                        '  ],',
                        '}',
                        '```',
                    ]),
                    'items' => [
                        'type' => 'integer',
                        'format' => 'int32',
                        'minimum' => 0,
                        'maximum' => 2,
                    ],
                ],
                'weapons' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 3,
                    'description' => implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Weapons loaned in each wave')),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Weapon'),
                            'app-weapon2',
                            SalmonMainWeapon2::find()
                                ->sorted()
                                ->all(),
                            null,
                            null,
                            null,
                            ['splatnet'],
                        ),
                    ]),
                    'items' => static::oapiKey(
                        implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Weapon')),
                        ]),
                        ArrayHelper::getColumn(
                            SalmonMainWeapon2::find()
                                ->orderBy(['key' => SORT_ASC])
                                ->all(),
                            'key',
                            false,
                        ),
                        true, // replace description
                    ),
                ],
                'boss_kills' => [
                    'type' => 'object',
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Number of times the player kills each boss'),
                        '',
                        Yii::t(
                            'app-apidoc2',
                            'If not kills the boss, you can send `0` or omit the boss.',
                        ),
                    ]),
                    'properties' => ArrayHelper::map(
                        SalmonBoss2::find()->orderBy(['key' => SORT_ASC])->all(),
                        'key',
                        fn (SalmonBoss2 $boss): array => [
                                'type' => 'integer',
                                'format' => 'int32',
                                'minimum' => 0,
                                'description' => implode("\n", [
                                    Html::encode(Yii::t(
                                        'app-apidoc2',
                                        'Number of times the player kills {boss}',
                                        ['boss' => Yii::t('app-salmon-boss2', $boss->name)],
                                    )),
                                ]),
                            ],
                    ),
                ],
            ],
            'example' => static::openApiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            SplatNet2PrincipalID::class,
        ];
    }

    public static function openApiExample(): array
    {
        return [
            'splatnet_id' => '3f6fb10a91b0c551',
            'name' => 'HINA',
            'special' => 'presser',
            'rescue' => 3,
            'death' => 3,
            'golden_egg_delivered' => 13,
            'power_egg_collected' => 318,
            'species' => 'inkling',
            'gender' => 'girl',
            'special_uses' => [0, 1],
            'weapons' => ['wakaba', 'hydra'],
            'boss_kills' => [
                'maws' => 2,
                'stinger' => 1,
            ],
        ];
    }
}
