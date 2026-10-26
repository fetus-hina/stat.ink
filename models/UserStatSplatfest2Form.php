<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class UserStatSplatfest2Form extends Model
{
    use ValidateDataTrait;

    /**
     * @var string|null
     */
    public $region;

    #[Override]
    public function rules()
    {
        return [
            [['region'], 'default', 'value' => null],
            [['region'], 'string'],
            [['region'], 'exist',
                'targetClass' => Region2::class,
                'targetAttribute' => ['region' => 'key'],
            ],
        ];
    }
}
