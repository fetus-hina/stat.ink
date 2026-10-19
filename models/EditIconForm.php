<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class EditIconForm extends Model
{
    use ValidateDataTrait;

    public $image;

    #[Override]
    public function rules()
    {
        return [
            [['image'], 'required', 'message' => 'Please upload a file.'],
            [['image'], 'image',
                'extensions' => ['png', 'jpg'],
                'maxSize' => 2 * 1024 * 1024,
                'maxWidth' => 2000,
                'maxHeight' => 2000,
            ],
        ];
    }
}
