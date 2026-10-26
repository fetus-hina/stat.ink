<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class InternalActivityForm extends Model
{
    use ValidateDataTrait;

    /** @var string|array<mixed>|null */
    public $screen_name;
    /** @var string|array<mixed>|null */
    public $only;

    #[Override]
    public function rules()
    {
        return [
            [['screen_name'], 'required'],
            [['screen_name', 'only'], 'string'],
            [['screen_name'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
            [['only'], 'in', 'range' => ['spl1', 'spl2', 'spl3', 'salmon2', 'salmon3']],
        ];
    }
}
