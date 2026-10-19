<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class ApiV1BattleListForm extends Model
{
    use ValidateDataTrait;

    public $id;
    public $screen_name;
    public $count;
    public $newer_than;
    public $older_than;

    #[Override]
    public function rules()
    {
        return [
            [['id'], 'exist',
                'targetClass' => Battle::className(),
                'targetAttribute' => 'id',
            ],
            [['screen_name'], 'exist',
                'targetClass' => User::className(),
                'targetAttribute' => 'screen_name',
            ],
            [['newer_than', 'older_than'], 'integer'],
            [['count'], 'default', 'value' => 10],
            [['count'], 'integer', 'min' => 1, 'max' => 100],
        ];
    }
}
