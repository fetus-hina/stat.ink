<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class ApiV2UserStatsForm extends Model
{
    use ValidateDataTrait;

    public $screen_name;

    #[Override]
    public function rules()
    {
        return [
            [['screen_name'], 'required'],
            [['screen_name'], 'string'],
            [['screen_name'], 'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
        ];
    }
}
