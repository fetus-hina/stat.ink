<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class FestViewForm extends Model
{
    use ValidateDataTrait;

    /** @var string|array<mixed>|null */
    public $region;
    /** @var string|array<mixed>|null */
    public $order;

    #[Override]
    public function rules()
    {
        return [
            [['region', 'order'], 'required'],
            [['region'], 'string', 'min' => 2, 'max' => 2],
            [['order'], 'integer', 'min' => 1],
        ];
    }
}
