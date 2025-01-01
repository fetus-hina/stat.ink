<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use Yii;
use app\models\Battle;
use app\models\User;
use app\models\openapi\Apikey;
use app\models\openapi\Util;
use yii\base\Model;

use function count;
use function filter_var;
use function implode;
use function is_array;
use function is_scalar;
use function vsprintf;

use const FILTER_VALIDATE_INT;

class DeleteBattleForm extends Model
{
    use Util;

    // API
    public $apikey;
    public $test;

    // target
    public $id;

    // read-only properties
    public $deletedIdList = [];
    public $errorIdList = [];

    public function rules()
    {
        return [
            [['apikey', 'id'], 'required'],
            [['apikey'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'api_key',
            ],
            [['test'], 'in', 'range' => ['validate', 'dry_run']],
            [['id'], 'validateBattleId'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function validateBattleId($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (is_scalar($this->$attribute)) {
            $this->$attribute = [$this->$attribute];
        }

        if (!is_array($this->$attribute)) {
            $this->addError($attribute, "{$attribute} should be an array of or scalar ID.");
            return;
        }

        if (count($this->$attribute) > 100) {
            $this->addError($attribute, 'too many values.');
            return;
        }

        $valueErrors = [];
        foreach ($this->$attribute as $id) {
            if (!is_scalar($id)) {
                $this->addError($attribute, "{$attribute} should be an array of or scalar ID.");
                return;
            }
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                $valueErrors[] = (string)$id;
            }
        }

        if ($valueErrors) {
            $this->addError($attribute, vsprintf('%s has non-integer value(s): %s', [
                $attribute,
                implode(', ', $valueErrors),
            ]));
            return;
        }
    }

    public function save()
    {
        $this->deletedIdList = [];
        $this->errorIdList = [];

        if ($this->hasErrors()) {
            return false;
        }

        if (!$user = User::findOne(['api_key' => $this->apikey])) {
            $this->addError('apikey', 'User does not exist.');
            return false;
        }

        foreach ($this->id as $id) {
            $battle = Battle::findOne(['id' => (int)(string)$id]);
            if (!$battle) {
                $this->errorIdList[] = [
                    'id' => $id,
                    'error' => 'not found',
                ];
                continue;
            }

            if ($battle->user_id != $user->id) {
                $this->errorIdList[] = [
                    'id' => $id,
                    'error' => 'user not match',
                ];
                continue;
            }

            if ($battle->is_automated) {
                $this->errorIdList[] = [
                    'id' => $id,
                    'error' => 'automated result',
                ];
                continue;
            }

            if (!$this->test) {
                $battle->delete();
            }

            $this->deletedIdList[] = [
                'id' => $id,
                'error' => null,
            ];
        }

        return true;
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Delete information'),
            'properties' => [
                'apikey' => static::oapiRef(Apikey::class),
                'id' => [
                    'description' => Yii::t('app-apidoc1', 'ID(s) to be deleted'),
                    'oneOf' => [
                        [
                            'type' => 'integer',
                            'format' => 'int64',
                        ],
                        [
                            'type' => 'array',
                            'items' => [
                                'type' => 'integer',
                                'format' => 'int64',
                            ],
                        ],
                    ],
                ],
                'test' => [
                    'type' => 'string',
                    'description' => implode("\n", [
                        Yii::t('app-apidoc1', 'For testing'),
                        '',
                        static::oapiKeyValueTable(
                            Yii::t('app-apidoc1', 'Action'),
                            'app-apidoc1',
                            [
                                [
                                    'key' => 'validate',
                                    'name' => 'Validate only. Returns simple result.',
                                ],
                                [
                                    'key' => 'dry_run',
                                    'name' => 'Do more action but not to be deleted.',
                                ],
                            ],
                        ),
                    ]),
                    'enum' => [
                        'validate',
                        'dry_run',
                    ],
                ],
            ],
            'example' => [
                'apikey' => 'fw50hytJKRe91FHuL4-K_SnzQ9Fwgwf2t_It3mQSuBU',
                'id' => [42, 100, 101],
                'test' => null,
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            Apikey::class,
        ];
    }

    public static function openapiExample(): array
    {
        return [
        ];
    }
}
