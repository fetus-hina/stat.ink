<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\battle3FilterForm\DropdownListTrait;
use app\models\battle3FilterForm\PermalinkTrait;
use app\models\battle3FilterForm\QueryDecoratorTrait;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_merge;
use function sprintf;

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
                'range' => self::getKeyList(Season3::class, self::PREFIX_TERM_SEASON),
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
            'rule' => Yii::t('app', 'Mode'),
            'map' => Yii::t('app', 'Stage'),
            'weapon' => Yii::t('app', 'Weapon'),
            'result' => Yii::t('app', 'Result'),
            'knockout' => Yii::t('app', 'Knockout'),
            'term' => Yii::t('app', 'Term'),
        ];
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
}
