<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

final class KDWin3FilterForm extends Model
{
    public ?string $lobby = null;
    public ?string $season = null;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'filter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lobby', 'season'], 'string'],
            [['lobby'], 'exist', 'skipOnError' => true,
                'targetClass' => Lobby3::class,
                'targetAttribute' => 'key',
            ],
            [['season'], 'exist', 'skipOnError' => true,
                'targetClass' => Season3::class,
                'targetAttribute' => 'key',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
