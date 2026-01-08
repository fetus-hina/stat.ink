<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class Knockout2FilterForm extends Model
{
    public $lobby;
    public $rank;

    public function rules()
    {
        return [
            [['lobby', 'rank'], 'string'],
            [['lobby', 'rank'], 'trim'],
            [['lobby'], 'in',
                'range' => [
                    'standard',
                    'squad',
                    'squad_2',
                    'squad_4',
                ],
            ],
            [['rank'], 'exist',
                'targetClass' => RankGroup2::class,
                'targetAttribute' => 'key',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'lobby' => Yii::t('app', 'Lobby'),
            'rank' => Yii::t('app', 'Rank'),
        ];
    }
}
