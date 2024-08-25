<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;
use app\models\salmon3FilterForm\DropdownListTrait;
use app\models\salmon3FilterForm\PermalinkTrait;
use app\models\salmon3FilterForm\QueryDecoratorTrait;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_merge;
use function filter_var;
use function hash_hmac;
use function is_array;
use function is_int;
use function is_string;
use function preg_match;
use function substr;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;

final class Salmon3FilterForm extends Model
{
    use DropdownListTrait;
    use PermalinkTrait;
    use QueryDecoratorTrait;

    public const LOBBY_ALL = '';
    public const LOBBY_BIG_RUN = 'bigrun';
    public const LOBBY_EGGSTRA_WORK = 'eggstra';
    public const LOBBY_NORMAL = 'normal';
    public const LOBBY_NOT_PRIVATE = '!private';
    public const LOBBY_PRIVATE = 'private';

    public const RESULT_CLEARED = 'cleared';
    public const RESULT_CLEARED_KING_APPEAR = 'cleared-king-appear';
    public const RESULT_CLEARED_KING_DEFEAT = 'cleared-king-defeat';
    public const RESULT_CLEARED_KING_FAILED = 'cleared-king-failed';
    public const RESULT_FAILED = 'failed';
    public const RESULT_FAILED_W1 = 'failed-w1';
    public const RESULT_FAILED_W2 = 'failed-w2';
    public const RESULT_FAILED_W3 = 'failed-w3';
    public const RESULT_FAILED_W4 = 'failed-w4';
    public const RESULT_FAILED_W5 = 'failed-w5';

    public string|null $lobby = null;
    public string|null $map = null;
    public string|null $result = null;
    public string|null $term = null;
    public string|null $term_from = null;
    public string|null $term_to = null;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'f';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lobby', 'map', 'result', 'term', 'term_from', 'term_to'], 'string'],

            [['lobby'], 'in',
                'range' => [
                    // self::LOBBY_ALL,
                    self::LOBBY_BIG_RUN,
                    self::LOBBY_EGGSTRA_WORK,
                    self::LOBBY_NORMAL,
                    self::LOBBY_NOT_PRIVATE,
                    self::LOBBY_PRIVATE,
                ],
            ],
            [['map'], 'in',
                'range' => Yii::$app->cache->getOrSet(
                    hash_hmac('sha256', 'map', __FILE__),
                    fn (): array => array_merge(
                        self::getKeyList(SalmonMap3::class),
                        self::getBigRunKeys(),
                    ),
                    7200,
                ),
            ],
            [['result'], 'in',
                'range' => [
                    self::RESULT_CLEARED,
                    self::RESULT_CLEARED_KING_APPEAR,
                    self::RESULT_CLEARED_KING_DEFEAT,
                    self::RESULT_CLEARED_KING_FAILED,
                    self::RESULT_FAILED,
                    self::RESULT_FAILED_W1,
                    self::RESULT_FAILED_W2,
                    self::RESULT_FAILED_W3,
                    self::RESULT_FAILED_W4,
                    self::RESULT_FAILED_W5,
                ],
            ],
            [['term'], 'in',
                'range' => self::getKeyListFromDropdown(...$this->getTermDropdown()),
            ],
            [['term_from', 'term_to'], 'required',
                'when' => fn (self $model): bool => $model->term === 'term',
            ],
            [['term_from', 'term_to'], 'datetime',
                'format' => 'php:Y-m-d H:i:s',
                'when' => fn (self $model, string $attribute): bool => substr((string)$model->$attribute, 0, 1) !== '@',
            ],
            [['term_from', 'term_to'], 'match',
                'pattern' => '/^@\d+$/',
                'when' => fn (self $model, string $attribute): bool => substr((string)$model->$attribute, 0, 1) === '@',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lobby' => Yii::t('app', 'Lobby'),
            'map' => Yii::t('app', 'Stage'),
            'result' => Yii::t('app', 'Result'),
            'term' => Yii::t('app', 'Term'),
            'term_from' => Yii::t('app', 'Period From'),
            'term_to' => Yii::t('app', 'Period To'),
        ];
    }

    public function updateUnixtimeToString(): void
    {
        if ($this->term === 'term') {
            $this->term_from = self::unixtimeToString($this->term_from);
            $this->term_to = self::unixtimeToString($this->term_to);
        }
    }

    private static function unixtimeToString(?string $value): ?string
    {
        if (
            !is_string($value) ||
            !preg_match('/^@(\d+)$/', $value, $match)
        ) {
            return $value;
        }

        $timestamp = filter_var($match[1], FILTER_VALIDATE_INT);
        if (!is_int($timestamp)) {
            return $value;
        }

        try {
            return (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone)))
                ->setTimestamp($timestamp)
                ->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
        }
        return $value;
    }

    /**
     * @param class-string<ActiveRecord> $modelClass
     * @return string[]
     */
    private static function getKeyList(string $modelClass): array
    {
        return Yii::$app->cache->getOrSet(
            hash_hmac('sha256', $modelClass, __METHOD__),
            fn (): array => ArrayHelper::getColumn(
                $modelClass::find()->orderBy(['key' => SORT_ASC])->all(),
                'key',
            ),
            600,
        );
    }

    /**
     * @return string[]
     */
    private static function getKeyListFromDropdown(array $options, mixed $optionsUnused = null): array
    {
        $results = [];
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                // optgroup
                $results = array_merge($results, self::getKeyListFromDropdown($value));
            } else {
                $results[] = $key;
            }
        }

        return $results;
    }

    /**
     * @return string[]
     */
    private static function getBigRunKeys(): array
    {
        return ArrayHelper::getColumn(
            BigrunMap3::find()
                ->orderBy(['key' => SORT_ASC])
                ->all(),
            'key',
        );
    }
}
