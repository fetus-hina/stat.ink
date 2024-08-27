<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

final class RenameScreenNameForm extends Model
{
    public string|null $screen_name = null;

    public function rules()
    {
        return [
            [['screen_name'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => Yii::t('app', '{attribute} must be at most 15 alphanumeric or underscore characters.'),
            ],
            [['screen_name'], 'compare',
                'compareValue' => Yii::$app->user->identity?->screen_name,
                'operator' => '!==',
                'message' => Yii::t('app', 'You cannot use the same {attribute} as your current one.'),
            ],
            [['screen_name'], 'unique',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name'],
                'message' => Yii::t('app', 'This {attribute} is already in use.'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app', 'New Screen Name (Login Name)'),
        ];
    }
}
