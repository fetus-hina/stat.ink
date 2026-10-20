<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

class KDWin2FilterForm extends Model
{
    /** @var string|array<mixed>|null */
    public $map;
    /** @var string|array<mixed>|null */
    public $weapon;
    /** @var string|array<mixed>|null */
    public $rank;
    /** @var string|array<mixed>|null */
    public $version;

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['map', 'weapon', 'rank', 'version'], 'string'],
            [['map'], 'exist',
                'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'skipOnError' => true,
                'targetClass' => WeaponType2::class,
                'targetAttribute' => 'key',
            ],
            [['rank'], 'exist',
                'skipOnError' => true,
                'targetClass' => RankGroup2::class,
                'targetAttribute' => 'key',
            ],
            [['version'], 'exist',
                'skipOnError' => true,
                'targetClass' => SplatoonVersionGroup2::class,
                'targetAttribute' => 'tag',
                'when' => fn (): bool => $this->version !== '*',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }
}
