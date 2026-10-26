<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class ApiV2BattleListForm extends Model
{
    use ValidateDataTrait;

    /** @var string|array<mixed>|null */
    public $screen_name;
    /** @var string|array<mixed>|null */
    public $only;
    /** @var string|array<mixed>|null */
    public $newer_than;
    /** @var string|array<mixed>|null */
    public $older_than;
    /** @var string|array<mixed>|null */
    public $order;
    /** @var string|array<mixed>|null */
    public $count;

    #[Override]
    public function rules()
    {
        return [
            [['screen_name'], 'required',
                'when' => fn ($model) => ($model->only === 'splatnet_number') ||
                    ($model->order === 'splatnet_asc') ||
                    ($model->order === 'splatnet_desc'),
            ],
            [['screen_name'], 'string'],
            [['screen_name'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name' => 'screen_name'],
            ],
            [['only'], 'string'],
            [['only'], 'in',
                'range' => [
                    'splatnet_number',
                ],
            ],
            [['newer_than', 'older_than'], 'integer', 'min' => 1],
            [['order'], 'string'],
            [['order'], 'in',
                'range' => [
                    'asc',
                    'desc',
                    'splatnet_asc',
                    'splatnet_desc',
                ],
            ],
            [['count'], 'integer', 'min' => 1, 'max' => 50,
                'when' => fn ($model): bool => $model->only !== 'splatnet_number',
            ],
            [['count'], 'integer', 'min' => 1, 'max' => 1000,
                'when' => fn ($model): bool => $model->only === 'splatnet_number',
            ],
        ];
    }
}
