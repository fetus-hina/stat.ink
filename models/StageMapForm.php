<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class StageMapForm extends Model
{
    use ValidateDataTrait;

    /**
     * @var string|null
     */
    public $map;

    #[Override]
    public function rules()
    {
        return [
            [['map'], 'required'],
            [['map'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'key',
            ],
        ];
    }
}
