<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class MachineTranslationForm extends Model
{
    use ValidateDataTrait;

    /**
     * @var string|null
     */
    public $direction;

    #[Override]
    public function rules()
    {
        return [
            [['direction'], 'required'],
            [['direction'], 'in',
                'range' => ['enable', 'disable'],
                'strict' => true,
            ],
        ];
    }
}
