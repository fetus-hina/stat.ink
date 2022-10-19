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
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
            [['lobby'], 'exist',
                'targetClass' => Lobby3::class,
                'targetAttribute' => 'key',
            ],
            [['rule'], 'exist',
                'targetClass' => Rule3::class,
                'targetAttribute' => 'key',
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
}
