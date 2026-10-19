<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class UserLanguageForm extends Model
{
    use ValidateDataTrait;

    public $language;

    #[Override]
    public function rules()
    {
        return [
            [['language'], 'required'],
            [['language'], 'exist',
                'targetClass' => Language::className(),
                'targetAttribute' => 'lang',
            ],
        ];
    }
}
