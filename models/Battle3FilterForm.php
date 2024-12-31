<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;
use app\models\battle3FilterForm\DropdownListTrait;
use app\models\battle3FilterForm\PermalinkTrait;
use app\models\battle3FilterForm\QueryDecoratorTrait;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_merge;
use function filter_var;
use function is_array;
use function is_int;
use function is_string;
use function preg_match;
use function sprintf;
use function substr;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;

final class Battle3FilterForm extends Model
{
    use DropdownListTrait;
    use PermalinkTrait;
    use QueryDecoratorTrait;

    public const PREFIX_WEAPON_MAIN = '~';
    public const PREFIX_WEAPON_SPECIAL = '*';
    public const PREFIX_WEAPON_SUB = '+';
    public const PREFIX_WEAPON_TYPE = '@';
    public const PREFIX_TERM_SEASON = '@';

    public const RESULT_NOT_DRAW = '~not_draw';
    public const RESULT_NOT_WIN = '~not_win';
    public const RESULT_UNKNOWN = '~unknown';
    public const RESULT_VIRTUAL_LOSE = '~lose';
    public const RESULT_WIN_OR_LOSE = '~win_lose';

    public const LOBBY_NOT_PRIVATE = '!private';

    public ?string $lobby = null;
    public ?string $rule = null;
    public ?string $map = null;
    public ?string $weapon = null;
    public ?string $result = null;
    public ?string $knockout = null;
    public ?string $term = null;
    public ?string $term_from = null;
    public ?string $term_to = null;
    public ?string $played_with = null;

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
            [['lobby', 'rule', 'map', 'weapon', 'result', 'knockout', 'term'], 'string'],
            [['term_from', 'term_to', 'played_with'], 'string'],

            [['lobby'], 'in',
                'range' => array_merge(
                    self::getKeyList(Lobby3::class),
                    self::getKeyList(LobbyGroup3::class, '@'),
                    [self::LOBBY_NOT_PRIVATE],
                ),
            ],
            [['rule'], 'in',
                'range' => array_merge(
                    self::getKeyList(Rule3::class),
                    self::getKeyList(RuleGroup3::class, '@'),
                ),
            ],
            [['map'], 'exist',
                'targetClass' => Map3::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'in',
                'range' => array_merge(
                    self::getKeyList(Weapon3::class),
                    self::getKeyList(WeaponType3::class, self::PREFIX_WEAPON_TYPE),
                    self::getKeyList(Subweapon3::class, self::PREFIX_WEAPON_SUB),
                    self::getKeyList(Special3::class, self::PREFIX_WEAPON_SPECIAL),
                    self::getKeyList(Mainweapon3::class, self::PREFIX_WEAPON_MAIN),
                ),
            ],
            [['result'], 'in',
                'range' => array_merge(self::getKeyList(Result3::class), [
                    self::RESULT_NOT_DRAW,
                    self::RESULT_NOT_WIN,
                    self::RESULT_UNKNOWN,
                    self::RESULT_VIRTUAL_LOSE,
                    self::RESULT_WIN_OR_LOSE,
                ]),
            ],
            [['knockout'], 'in',
                'range' => ['yes', 'no'],
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
            [['played_with'], 'match',
                'pattern' => '/^[0-9a-f]{32}$/',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'knockout' => Yii::t('app', 'Knockout'),
            'lobby' => Yii::t('app', 'Lobby'),
            'map' => Yii::t('app', 'Stage'),
            'played_with' => Yii::t('app', 'Played With'),
            'result' => Yii::t('app', 'Result'),
            'rule' => Yii::t('app', 'Mode'),
            'term' => Yii::t('app', 'Term'),
            'term_from' => Yii::t('app', 'Period From'),
            'term_to' => Yii::t('app', 'Period To'),
            'weapon' => Yii::t('app', 'Weapon'),
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
     */
    private static function getKeyList(string $modelClass, ?string $prefix = null): array
    {
        return ArrayHelper::getColumn(
            $modelClass::find()->orderBy(['key' => SORT_ASC])->all(),
            fn (ActiveRecord $model): string => $prefix !== null
                ? sprintf('%s%s', $prefix, $model->key)
                : $model->key,
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
}
