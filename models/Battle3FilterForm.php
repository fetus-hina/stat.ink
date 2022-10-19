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

final class Battle3FilterForm extends Model
{
    use DropdownListTrait;
    use PermalinkTrait;
    use QueryDecoratorTrait;

    public ?string $lobby = null;
    public ?string $rule = null;
    public ?string $map = null;

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
            [['lobby', 'rule', 'map'], 'string'],

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
        ];
    }

    /**
     * @param class-string<ActiveRecord> $modelClass
     */
    private static function getKeyList(string $modelClass, ?string $prefix = null): array
    {
        return ArrayHelper::getColumn(
            $modelClass::find()->all(),
            fn (ActiveRecord $model): string => ($prefix !== null)
                ? \sprintf('%s%s', $prefix, $model->key)
                : $model->key,
        );
    }
}
