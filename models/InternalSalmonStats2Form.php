<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class InternalSalmonStats2Form extends Model
{
    use ValidateDataTrait;

    /** @var string|array<mixed>|null */
    public $screen_name;

    #[Override]
    public function rules()
    {
        return [
            [['screen_name'], 'trim'],
            [['screen_name'], 'required'],
            [['screen_name'], 'string'],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
            ],
        ];
    }
}
