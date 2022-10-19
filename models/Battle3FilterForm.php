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
            [['map'], 'string'],
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
            'map' => Yii::t('app', 'Stage'),
        ];
    }
}
