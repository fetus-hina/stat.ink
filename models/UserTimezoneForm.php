<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class UserTimezoneForm extends Model
{
    use ValidateDataTrait;

    public $timezone;

    #[Override]
    public function rules()
    {
        return [
            [['timezone'], 'required'],
            [['timezone'], 'exist',
                'targetClass' => Timezone::className(),
                'targetAttribute' => 'identifier',
            ],
        ];
    }
}
