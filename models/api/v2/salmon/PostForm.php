<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v2\salmon;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\behaviors\AutoTrimAttributesBehavior;
use app\components\helpers\Battle as BattleHelper;
use app\models\Agent;
use app\models\Salmon2;
use app\models\SalmonBoss2;
use app\models\SalmonBossAppearance2;
use app\models\SalmonFailReason2;
use app\models\SalmonMap2;
use app\models\SalmonTitle2;
use app\models\openapi\SplatNet2ID;
use app\models\openapi\Util as OpenAPIUtil;
use jp3cki\uuid\Uuid;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\UnsetArrayValue;
use yii\validators\NumberValidator;

class PostForm extends Model
{
    use OpenAPIUtil;

    // Recommended UUID NS, splatnetNumber@principalID
    public const UUID_NAMESPACE_BY_PRINCIPAL_ID = '418fe150-cb33-11e8-8816-d050998473ba';

    // If splatnet_number present
    public const UUID_NAMESPACE_BY_SPLATNET_AND_USER_ID = 'b03116da-cbae-11e8-a7fa-d050998473ba';

    // If non-UUID
    public const UUID_NAMESPACE_BY_FREETEXT = 'b007a6f6-cbae-11e8-aa3e-d050998473ba';

    public $uuid;
    public $splatnet_number;
    public $stage;
    public $clear_waves;
    public $fail_reason;
    public $title;
    public $title_exp;
    public $title_after;
    public $title_exp_after;
    public $danger_rate;
    public $boss_appearances;
    public $waves;
    public $my_data;
    public $teammates;
    public $shift_start_at;
    public $start_at;
    public $end_at;
    public $note;
    public $private_note;
    public $link_url;
    public $automated;
    public $agent;
    public $agent_version;

    public $is_found = false;
    private $uuid_formatted;

    public function behaviors()
    {
        return [
            AutoTrimAttributesBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['uuid', 'stage', 'fail_reason', 'title', 'title_after'], 'string'],
            [['note', 'private_note', 'agent', 'agent_version'], 'string'],
            [['splatnet_number'], 'integer'],
            [['clear_waves'], 'integer', 'min' => 0, 'max' => 3],
            [['title_exp', 'title_exp_after'], 'integer', 'min' => 0, 'max' => 999],
            [['danger_rate'], 'number', 'min' => 0, 'max' => 999.9],
            [['shift_start_at', 'start_at', 'end_at'], 'integer'],
            [['link_url'], 'url'],
            [['automated'], 'in', 'range' => ['yes', 'no']],
            [['agent'], 'string', 'max' => 64],
            [['agent_version'], 'string', 'max' => 255],
            [['agent', 'agent_version'], 'required',
                'when' => function (self $model, string $attrName): bool {
                    return trim((string)$model->agent) !== '' ||
                        trim((string)$model->agent_version) !== '';
                },
            ],
            [['stage'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['stage' => 'key'],
            ],
            [['fail_reason'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonFailReason2::class,
                'targetAttribute' => 'key',
            ],
            [['title', 'title_after'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonTitle2::class,
                'targetAttribute' => 'key',
            ],
            [['boss_appearances'], 'validateBossAppearances'],
            [['waves'], 'validateWaves'],
            [['my_data'], 'validateMyData'],
            [['teammates'], 'validateTeammates'],
        ];
    }

    public function validateBossAppearances()
    {
        if ($this->hasErrors('boss_appearances')) {
            return;
        }

        if ($this->boss_appearances === null || $this->boss_appearances === '') {
            $this->boss_appearances = null;
            return;
        }

        if (!is_array($this->boss_appearances)) {
            $this->addError('boss_appearances', 'boss_appearances should be an associative array');
            return;
        }

        if (empty($this->boss_appearances)) {
            $this->boss_appearances = null;
            return;
        }

        $countValidator = Yii::createObject([
            'class' => NumberValidator::class,
            'integerOnly' => true,
            'min' => 0,
        ]);
        foreach ($this->boss_appearances as $key => $value) {
            $boss = SalmonBoss2::findOne(['key' => (string)$key]);
            if (!$boss) {
                $this->addError('boss_appearances', sprintf('unknown key "%s"', (string)$key));
                continue;
            }

            $error = null;
            if (!$countValidator->validate($value, $error)) {
                $this->addError('boss_appearances', sprintf('%s: %s', $key, $error));
                continue;
            }
        }
    }

    public function validateWaves()
    {
        if ($this->hasErrors('waves')) {
            return;
        }

        if ($this->waves === null || $this->waves === '') {
            $this->waves = null;
            return;
        }

        if (!is_array($this->waves)) {
            $this->addError('waves', 'waves should be an array');
            return;
        }

        if (empty($this->waves)) {
            $this->waves = null;
            return;
        }

        if (!ArrayHelper::isIndexed($this->waves)) {
            $this->addError('waves', 'waves should be an array (not associative array)');
            return;
        }

        if (count($this->waves) > 3) {
            $this->addError('waves', 'too many waves');
            return;
        }

        for ($i = 0; $i < 3; ++$i) {
            $wave = $this->waves[$i] ?? null;
            if (!$wave) {
                break;
            }

            $model = Yii::createObject(Wave::class);
            $model->attributes = $wave;
            if (!$model->validate()) {
                foreach ($model->getErrors() as $key => $errors) {
                    foreach ((array)$errors as $error) {
                        $this->addError('waves', sprintf('%s: %s', $key, $error));
                    }
                }
            }
        }
    }

    public function validateMyData(string $attribute, $params): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if ($this->$attribute === null || $this->$attribute == '') {
            $this->$attribute = null;
            return;
        }

        $this->validatePlayer($this->$attribute, $attribute, $attribute, true);
    }

    public function validateTeammates(string $attribute, $params): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if ($this->$attribute === null || $this->$attribute == '') {
            $this->$attribute = null;
            return;
        }

        if (empty($this->$attribute)) {
            $this->$attribute = null;
            return;
        }

        if (!ArrayHelper::isIndexed($this->$attribute)) {
            $this->addError($attribute, "{$attribute} should be an array (not associative array)");
            return;
        }

        if (count($this->$attribute) > 3) {
            $this->addError($attribute, 'too many players');
            return;
        }

        for ($i = 0; $i < 3; ++$i) {
            if (!$data = $this->$attribute[$i] ?? null) {
                return;
            }

            $this->validatePlayer($data, $attribute, "{$attribute}[{$i}]", false);
        }
    }

    protected function validatePlayer(
        $data, // array | stdclass
        string $attribute, // "players"
        string $attributeLabel, // "players[0]"
        bool $isMe
    ): void {
        $model = $this->playerFormInstantiation($data, $isMe);
        if (!$model) {
            $this->addError(
                $attribute,
                "{$attributeLabel} should be an instance of player structure"
            );
            return;
        }

        if ($model->validate()) {
            return;
        }

        foreach ($model->getErrors() as $attrName => $error) {
            foreach ((array)$error as $_) {
                $this->addError($attribute, "{$attributeLabel}.{$attrName}: {$_}");
            }
        }
    }

    protected function playerFormInstantiation($data, bool $isMe): ?Player
    {
        if (!is_array($data) && !is_object($data)) {
            return null;
        }

        $model = Yii::createObject(['class' => Player::class]);
        $model->attributes = $data;
        $model->is_me = $isMe ? 'yes' : 'no';
        return $model;
    }

    public function save(): ?Salmon2
    {
        if (!$this->validate()) {
            return null;
        }

        return Yii::$app->db->transactionEx(function (): ?Salmon2 {
            $this->is_found = false;
            if ($main = $this->findByUuid()) {
                $this->is_found = true;
                return $main;
            }

            if (!$main = $this->saveMain()) {
                return null;
            }

            if (!$this->saveBossAppearances($main)) {
                return null;
            }

            if (!$this->saveWaves($main)) {
                return null;
            }

            if (!$this->savePlayers($main)) {
                return null;
            }

            return $main;
        });
    }

    private function findByUuid(): ?Salmon2
    {
        $findThreshold = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->sub(new DateInterval('P1D')); // 24 hours, No DST because timezone is now UTC.

        return Salmon2::find()
            ->andWhere(['and',
                ['user_id' => Yii::$app->user->id],
                ['uuid' => $this->getUuidFormatted()],
                ['>=', 'created_at', $findThreshold->format(DateTime::ATOM)],
            ])
            ->limit(1)
            ->one();
    }

    private function saveMain(): ?Salmon2
    {
        return Yii::$app->db->transactionEx(function (): ?Salmon2 {
            $agent = $this->getAgent();
            $model = Yii::createObject(Salmon2::class);
            $model->attributes = [
                'user_id' => Yii::$app->user->id,
                'uuid' => $this->getUuidFormatted(),
                'splatnet_number' => $this->splatnet_number,
                'stage_id' => static::findRelatedId(SalmonMap2::class, $this->stage),
                'clear_waves' => $this->clear_waves,
                'fail_reason_id' => static::findRelatedId(
                    SalmonFailReason2::class,
                    $this->fail_reason
                ),
                'title_before_id' => static::findRelatedId(SalmonTitle2::class, $this->title),
                'title_before_exp' => $this->title_exp,
                'title_after_id' => static::findRelatedId(SalmonTitle2::class, $this->title_after),
                'title_after_exp' => $this->title_exp_after,
                'danger_rate' => ($this->danger_rate == '')
                    ? null
                    : sprintf('%.1f', (float)$this->danger_rate),
                'shift_period' => ($this->shift_start_at == '')
                    ? null
                    : BattleHelper::calcPeriod2((int)$this->shift_start_at),
                'start_at' => ($this->start_at == '')
                    ? null
                    : gmdate(\DateTime::ATOM, (int)$this->start_at),
                'end_at' => ($this->end_at == '')
                    ? null
                    : gmdate(\DateTime::ATOM, (int)$this->end_at),
                'note' => $this->note,
                'private_note' => $this->private_note,
                'link_url' => $this->link_url,
                'is_automated' => ($this->automated == '')
                    ? ($agent ? $agent->getIsAutomatedByDefault() : null)
                    : ($this->automated === 'yes'),
                'agent_id' => $agent->id ?? null,
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.2',
                'remote_port' => (int)($_SERVER['REMOTE_PORT'] ?? 0),
            ];
            if (!$model->save()) {
                return null;
            }

            return $model;
        });
    }

    private function saveBossAppearances(Salmon2 $salmon): bool
    {
        return Yii::$app->db->transactionEx(function () use ($salmon): bool {
            if (!$this->boss_appearances) {
                return true;
            }

            foreach ($this->boss_appearances as $key => $value) {
                if ($value > 0) {
                    $bossId = static::findRelatedId(SalmonBoss2::class, $key);
                    if ($bossId === null) {
                        return false;
                    }

                    $model = Yii::createObject([
                        'class' => SalmonBossAppearance2::class,
                        'salmon_id' => $salmon->id,
                        'boss_id' => $bossId,
                        'count' => (int)$value,
                    ]);
                    if (!$model->save()) {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    private function saveWaves(Salmon2 $salmon): bool
    {
        return Yii::$app->db->transactionEx(function () use ($salmon): bool {
            if (!$this->waves) {
                return true;
            }

            for ($i = 0; $i < 3; ++$i) {
                $wave = $this->waves[$i] ?? null;
                if (!$wave) {
                    return true;
                }

                $model = Yii::createObject(Wave::class);
                $model->attributes = $wave;

                if (!$model->save($salmon, $i + 1)) {
                    return false;
                }
            }

            return true;
        });
    }

    private function savePlayers(Salmon2 $salmon): bool
    {
        return Yii::$app->db->transactionEx(function () use ($salmon): bool {
            $transactPlayer = function ($data, bool $isMe) use ($salmon): bool {
                if (!$form = $this->playerFormInstantiation($data, $isMe)) {
                    return false;
                }

                return !!$form->save($salmon);
            };

            if ($this->my_data) {
                if (!$transactPlayer($this->my_data, true)) {
                    return false;
                }
            }

            if ($this->teammates) {
                for ($i = 0; $i < 3; ++$i) {
                    if (!$data = $this->teammates[$i] ?? null) {
                        break;
                    }

                    if (!$transactPlayer($data, false)) {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    public function getUuidFormatted(): string
    {
        if (!is_string($this->uuid_formatted)) {
            $this->uuid_formatted = $this->getUuidImpl()->formatAsString();
        }

        return $this->uuid_formatted;
    }

    private function getUuidImpl(): Uuid
    {
        if ($this->uuid != '') {
            try {
                $uuid = Uuid::fromString($this->uuid);
                switch ($uuid->getVersion()) {
                    case 1:
                    case 3:
                    case 4:
                    case 5:
                        return $uuid;

                    default:
                        break;
                }
            } catch (\Exception $e) {
            }

            return Uuid::v5(static::UUID_NAMESPACE_BY_FREETEXT, $this->uuid);
        }

        if ($this->splatnet_number != '') {
            if ($this->my_data) {
                if ($myData = $this->playerFormInstantiation($this->my_data, true)) {
                    if ($myData->splatnet_id) {
                        return Uuid::v5(
                            static::UUID_NAMESPACE_BY_PRINCIPAL_ID,
                            sprintf(
                                '%d@%s',
                                (int)$this->splatnet_number,
                                $myData->splatnet_id
                            )
                        );
                    }
                }
            }

            return Uuid::v5(
                static::UUID_NAMESPACE_BY_SPLATNET_AND_USER_ID,
                sprintf(
                    '%d@%d',
                    (int)$this->splatnet_number,
                    (int)Yii::$app->user->id
                )
            );
        }

        return Uuid::v4();
    }

    private function getAgent(): ?Agent
    {
        if ($this->agent == '' || $this->agent_version == '') {
            return null;
        }

        return Yii::$app->db->transactionEx(function (): ?Agent {
            $model = Agent::findOne([
                'name' => $this->agent,
                'version' => $this->agent_version,
            ]);
            if ($model) {
                return $model;
            }

            $model = Yii::createObject([
                'class' => Agent::class,
                'name' => $this->agent,
                'version' => $this->agent_version,
            ]);
            return $model->save() ? $model : null;
        });
    }

    private static function findRelatedId(string $class, ?string $key): ?int
    {
        if ($key === null || $key === '') {
            return null;
        }

        if (!$model = call_user_func([$class, 'findOne'], ['key' => $key])) {
            return null;
        }

        return (int)$model->id;
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Post the Salmon Run results'),
            'properties' => [
                'uuid' => [
                    'type' => 'string',
                    'description' => implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'A unique ID to identify the results')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'Client application should specify a UUID to detect duplicated shift.'
                        )),
                        '',
                        Html::encode(Yii::t('app-apidoc2', 'How to create the UUID:')),
                        '',
                        Html::encode(sprintf('- %s', Yii::t(
                            'app-apidoc2',
                            'SplatNet 2 based Application'
                        ))),
                        Html::encode(sprintf('  - %s', Yii::t(
                            'app-apidoc2',
                            'Generate a UUID version 5 with namespace `{ns}`.',
                            ['ns' => static::UUID_NAMESPACE_BY_PRINCIPAL_ID]
                        ))),
                        '',
                        Html::encode(sprintf('    %s', Yii::t(
                            'app-apidoc2',
                            'Use "`splatnet_number`@`principal_id`" format. (Example: `{example}`)',
                            ['example' => sprintf('%d@%s', 5436, '3f6fb10a91b0c551')]
                        ))) . '<br>',
                        sprintf('    %s', sprintf(
                            '`uuid_v5("%s", sprintf("%%d@%%s", number, principal_id))`',
                            addslashes(static::UUID_NAMESPACE_BY_PRINCIPAL_ID),
                        )),
                        '',
                        Html::encode(sprintf('- %s', Yii::t(
                            'app-apidoc2',
                            'Standalone Application (e.g., user\'s input or screen capture)'
                        ))),
                        Html::encode(sprintf('    - %s', Yii::t(
                            'app-apidoc2',
                            'Nothing send (Disabled duplicate detection)'
                        ))),
                        Html::encode(sprintf('    - %s', Yii::t(
                            'app-apidoc2',
                            'Generate a UUID version 4 on your side'
                        ))),
                        Html::encode(sprintf('    - %s', Yii::t(
                            'app-apidoc2',
                            'Generate a UUID version 3 or 5 on your side with your own namespace'
                        ))),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'The API endpoint will return `302 Found` if job has same UUID ' .
                            'posted in last 24 hours.'
                        ) . '  '),
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'This is helpful for unintended duplication, but it is helpless ' .
                            'for complate detect duplication.'
                        )),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'We recommend posting by the following procedure:'
                        )),
                        '',
                        Html::encode('  1. ' . Yii::t(
                            'app-apidoc2',
                            'Call [`GET {url}`]({link}) and retrieve already posted shift numbers.',
                            [
                                'url' => '/api/v2/user-salmon?only=splatnet_number',
                                'link' => '#operation/getUserSalmon',
                            ]
                        )),
                        '',
                        Html::encode('  2. ' . Yii::t(
                            'app-apidoc2',
                            'Fetch data from SplatNet 2.'
                        )),
                        '',
                        Html::encode('  3. ' . Yii::t(
                            'app-apidoc2',
                            'Filter unposted shifts and post to us.'
                        )),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'About UUID, refer [RFC 4122](https://tools.ietf.org/html/rfc4122).'
                        )),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'If omitted, we will automatically generate a random UUID.'
                        )),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'If the value is not correct as a UUID, we will use that value as a ' .
                            'seed to generate a UUID.'
                        )),
                        Html::encode(Yii::t('app-apidoc2', 'Do not rely on this behavior.')),
                    ]),
                ],
                'splatnet_number' => ArrayHelper::merge(SplatNet2ID::openApiSchema(), [
                    'nullable' => new UnsetArrayValue(),
                ]),
                'stage' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Stage')),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Stage'),
                            'app-salmon-map2',
                            SalmonMap2::find()->orderBy(['key' => SORT_ASC])->asArray()->all(),
                            null, // key column
                            null, // value column
                            null, // key label
                            'splatnet_hint'
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        SalmonMap2::find()->orderBy(['key' => SORT_ASC])->asArray()->all(),
                        'key',
                        false
                    ),
                    true // replace description
                ),
                'clear_waves' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'maximum' => 3,
                    'description' => implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'How many cleared waves')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            '`3` if cleared. `0` if failed in wave 1.'
                        )),
                    ]),
                ],
                'fail_reason' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Fail reason')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            '`null` or empty string if cleared or unknown'
                        )),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Reason'),
                            'app-salmon2',
                            SalmonFailReason2::find()
                                ->orderBy(['key' => SORT_ASC])
                                ->asArray()
                                ->all()
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        SalmonFailReason2::find()
                            ->orderBy(['key' => SORT_ASC])
                            ->asArray()
                            ->all(),
                        'key',
                        false
                    ),
                    true // replace description
                ),
                'title' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Title (before the shift)')),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc2', 'Title'),
                            'app-salmon-title2',
                            SalmonTitle2::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
                            null,
                            null,
                            null,
                            'splatnet',
                        ),
                    ]),
                    ArrayHelper::getColumn(
                        SalmonTitle2::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
                        'key',
                        false
                    ),
                    true // replace description
                ),
                'title_exp' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'maximum' => 999,
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Title points (before the shift)'),
                        '',
                        Yii::t('app-apidoc2', '`40` if Profreshional `40` of `999`'),
                    ]),
                ],
                'title_after' => static::oapiKey(
                    implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Title (after the shift)')),
                    ]),
                    ArrayHelper::getColumn(
                        SalmonTitle2::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
                        'key',
                        false
                    ),
                    true // replace description
                ),
                'title_exp_after' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'maximum' => 999,
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Title points (after the shift)'),
                        '',
                        Yii::t('app-apidoc2', '`40` if Profreshional `40` of `999`'),
                    ]),
                ],
                'danger_rate' => [
                    'type' => 'number',
                    'format' => 'float',
                    'minimum' => 0.0,
                    'maximum' => 200.0,
                    'multipleOf' => 0.1,
                    'description' => Yii::t(
                        'app-apidoc2',
                        'Hazard Level, 200.0 = "Hazard Level MAX!!"'
                    ),
                ],
                'boss_appearances' => (function (): array {
                    $models = SalmonBoss2::find()->orderBy(['key' => SORT_ASC])->all();

                    return [
                        'type' => 'object',
                        'description' => implode("\n", [
                            Yii::t('app-apidoc2', 'Boss appearances'),
                            '',
                            Html::encode(Yii::t(
                                'app-apidoc2',
                                'If your client doesn\'t/cannot detect this data, omit this ' .
                                'field or send just `null`.'
                            )),
                            '',
                            Yii::t(
                                'app-apidoc2',
                                'If not appearances the boss, you can send `0` or omit the boss.'
                            ),
                            '',
                            '```js',
                            '{',
                            '  "boss_appearances": null, // OK',
                            '}',
                            '',
                            '{',
                            '  "boss_appearances": { // OK: you can omit bosses if not necessary.',
                            '  },',
                            '}',
                            '',
                            '{',
                            '  "boss_appearances": {',
                            '    "scrapper": 0, // OK: you can send 0',
                            '  },',
                            '}',
                            '```',
                            '',
                            static::oapiKeyValueTable(
                                Yii::t('app-apidoc2', 'Boss'),
                                'app-salmon-boss2',
                                $models,
                                null, // key column
                                null, // value column
                                null, // key label
                                ['splatnet', 'splatnet_str'],
                            ),
                        ]),
                        'properties' => (function () use ($models): array {
                            $ret = [];
                            foreach ($models as $model) {
                                $ret[$model->key] = [
                                    'type' => 'integer',
                                    'format' => 'int32',
                                    'minimum' => 0,
                                    'description' => Yii::t('app-apidoc2', '{boss} appearances', [
                                        'boss' => Yii::t('app-salmon-boss2', $model->name),
                                    ]),
                                ];
                            }
                            return $ret;
                        })(),
                    ];
                })(),
                'waves' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 3,
                    'items' => static::oapiRef(Wave::class),
                    'description' => implode("\n", [
                        Html::encode(Yii::t('app-apidoc2', 'Information about each wave')),
                        '',
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'If your client doesn\'t/cannot detect this data, omit this field or ' .
                            'send just `null`.'
                        )),
                    ]),
                ],
                'my_data' => static::oapiRef(Player::class),
                'teammates' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 3,
                    'items' => static::oapiRef(Player::class),
                    'description' => implode("\n", [
                        Html::encode(Yii::t(
                            'app-apidoc2',
                            'Crew members\' (except `my_data`) data, typically have 3 elements'
                        )),
                    ]),
                ],
                'shift_start_at' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => Yii::t(
                        'app-apidoc2',
                        'The time when this rotation (play window) started in unix time format.'
                    ),
                ],
                'start_at' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => Yii::t(
                        'app-apidoc2',
                        'The time when this shift started in unix time format.',
                    ),
                ],
                'end_at' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => implode("\n", [
                        Yii::t(
                            'app-apidoc2',
                            'The time when this shift ended in unix time format.'
                        ),
                        '',
                        Yii::t(
                            'app-apidoc2',
                            'Note: this value may not be in SplatNet JSON.'
                        ),
                    ]),
                ],
                'note' => [
                    'type' => 'string',
                    'description' => Yii::t('app-apidoc2', 'User note'),
                ],
                'private_note' => [
                    'type' => 'string',
                    'description' => Yii::t('app-apidoc2', 'User\'s private note'),
                ],
                'link_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                    'description' => Yii::t(
                        'app-apidoc2',
                        'URL that related to this post. (e.g., YouTube video)'
                    ),
                ],
                'automated' => [
                    'type' => 'string',
                    'enum' => ['yes', 'no'],
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Is automated posting process?'),
                        '',
                        static::oapiKeyValueTable(
                            '',
                            'app-apidoc2',
                            [
                                ['key' => 'yes', 'name' => 'If automated.'],
                                ['key' => 'no', 'name' => 'If manual input.'],
                            ],
                            null,
                            null,
                            'value',
                        ),
                        '',
                        Yii::t(
                            'app-apidoc2',
                            'Choose `no` if this user\'s posts may be arbitrarily selected.'
                        ),
                    ]),
                ],
                'agent' => [
                    'type' => 'string',
                    'maxLength' => 64,
                    'example' => 'MyAwesomeClient',
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Name of your client'),
                        '',
                        Yii::t('app-apidoc2', 'This parameter is required if `{name}` set.', [
                            'name' => 'agent_version',
                        ]),
                    ]),
                ],
                'agent_version' => [
                    'type' => 'string',
                    'maxLength' => 255,
                    'example' => '1.0.0 (Windows 10)',
                    'description' => implode("\n", [
                        Yii::t('app-apidoc2', 'Version of your client'),
                        '',
                        Yii::t('app-apidoc2', 'This parameter is required if `{name}` set.', [
                            'name' => 'agent',
                        ]),
                    ]),
                ],
            ],
            'example' => static::openApiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            Player::class,
            Wave::class,
        ];
    }

    public static function openApiExample(): array
    {
        return [
            'uuid' => '4c705dd6-7a22-5f04-865d-d87413b0970d',
            'splatnet_number' => 5436,
            'stage' => 'tokishirazu',
            'clear_waves' => 1,
            'fail_reason' => 'wipe_out',
            'title' => 'profreshional',
            'title_exp' => 410,
            'title_after' => 'profreshional',
            'title_exp_after' => 405,
            'danger_rate' => 174.2,
            'boss_appearances' => [
                'drizzler' => 6,
                'flyfish' => 7,
                'maws' => 6,
                'scrapper' => 3,
                'steel_eel' => 4,
                'steelhead' => 5,
                'stinger' => 5,
            ],
            'waves' => [
                [
                    'known_occurrence' => null,
                    'water_level' => 'high',
                    'golden_egg_quota' => 18,
                    'golden_egg_appearances' => 45,
                    'golden_egg_delivered' => 24,
                    'power_egg_collected' => 846,
                ],
                [
                    'known_occurrence' => null,
                    'water_level' => 'normal',
                    'golden_egg_quota' => 20,
                    'golden_egg_appearances' => 33,
                    'golden_egg_delivered' => 19,
                    'power_egg_collected' => 681,
                ],
            ],
            'my_data' => Player::openApiExample(),
            'teammates' => null,
            'shift_start_at' => 1573106400,
            'start_at' => 1573151096,
            'end_at' => null,
            'note' => null,
            'private_note' => null,
            'link_url' => null,
            'automated' => 'yes',
            'agent' => 'splatnet2statink',
            'agent_version' => '1.5.3',
        ];
    }
}
