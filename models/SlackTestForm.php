<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class SlackTestForm extends Model
{
    use ValidateDataTrait;

    /**
     * @var int|string|null
     */
    public $id;

    #[Override]
    public function rules()
    {
        return [
            [['id'], 'exist', 'targetClass' => Slack::class, 'targetAttribute' => 'id'],
        ];
    }
}
