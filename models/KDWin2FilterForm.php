<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class KDWin2FilterForm extends Model
{
    public $map;
    public $weapon;
    public $rank;
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
