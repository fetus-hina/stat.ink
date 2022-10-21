<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\Weapon3;
use app\models\battle3FilterForm\DropdownListTrait;
use app\models\battle3FilterForm\PermalinkTrait;
use app\models\battle3FilterForm\QueryDecoratorTrait;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

final class Battle3FilterForm extends Model
{
    use DropdownListTrait;
    use PermalinkTrait;
    use QueryDecoratorTrait;

    public const PREFIX_WEAPON_TYPE = '@';
    public const PREFIX_WEAPON_SUB = '+';
    public const PREFIX_WEAPON_SPECIAL = '*';
    public const PREFIX_WEAPON_MAIN = '~';

    public ?string $lobby = null;
    public ?string $rule = null;
    public ?string $map = null;
    public ?string $weapon = null;

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
            [['lobby', 'rule', 'map', 'weapon'], 'string'],

            [['lobby'], 'in',
                'range' => \array_merge(
                    self::getKeyList(Lobby3::class),
                    self::getKeyList(LobbyGroup3::class, '@'),
                ),
            ],
            [['rule'], 'in',
                'range' => \array_merge(
                    self::getKeyList(Rule3::class),
                    self::getKeyList(RuleGroup3::class, '@'),
                ),
            ],
            [['map'], 'exist',
                'targetClass' => Map3::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'in',
                'range' => \array_merge(
                    self::getKeyList(Weapon3::class),
                    self::getKeyList(WeaponType3::class, self::PREFIX_WEAPON_TYPE),
                    self::getKeyList(Subweapon3::class, self::PREFIX_WEAPON_SUB),
                    self::getKeyList(Special3::class, self::PREFIX_WEAPON_SPECIAL),
                    self::getKeyList(Mainweapon3::class, self::PREFIX_WEAPON_MAIN),
                ),
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
        ];
    }

    /**
     * @param class-string<ActiveRecord> $modelClass
     */
    private static function getKeyList(string $modelClass, ?string $prefix = null): array
    {
        return ArrayHelper::getColumn(
            $modelClass::find()->orderBy(['key' => SORT_ASC])->all(),
            fn (ActiveRecord $model): string => ($prefix !== null)
                ? \sprintf('%s%s', $prefix, $model->key)
                : $model->key,
        );
    }
}
