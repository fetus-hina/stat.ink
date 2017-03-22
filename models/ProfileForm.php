<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $name;
    public $nnid;
    public $sw_friend_code;
    public $twitter;
    public $ikanakama;
    public $env;
    public $blackout;

    public function rules()
    {
        return [
            [['name', 'nnid', 'twitter', 'ikanakama', 'env'], 'filter',
                'filter' => function ($value) {
                    $value = trim((string)$value);
                    return $value === '' ? null : $value;
                },
            ],
            [['name', 'blackout'], 'required'],
            [['name'], 'string', 'max' => 15],
            [['nnid'], 'string', 'min' => 6, 'max' => 16],
            [['nnid'], 'match', 'pattern' => '/^[a-zA-Z0-9._-]+$/'],
            [['sw_friend_code'], 'string', 'min' => 12, 'max' => 17],
            [['sw_friend_code'], 'trim'],
            [['sw_friend_code'], 'match',
                'pattern' => '/^(?:SW-?)?\d{4}-?\d{4}-?\d{4}$/i',
            ],
            [['twitter'], 'string', 'max' => 15],
            [['twitter'], 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/'],
            [['ikanakama'], 'integer', 'min' => 1],
            [['env'], 'string'],
            [['blackout'], 'in',
                'range' => [
                    User::BLACKOUT_NOT_BLACKOUT,
                    User::BLACKOUT_NOT_PRIVATE,
                    User::BLACKOUT_NOT_FRIEND,
                    User::BLACKOUT_ALWAYS,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'   => Yii::t('app', 'Screen Name (Login Name)'),
            'name'          => Yii::t('app', 'Name (for display)'),
            'nnid'          => Yii::t('app', 'Nintendo Network ID'),
            'sw_friend_code' => Yii::t('app', 'Friend Code (Switch)'),
            'twitter'       => Yii::t('app', 'Twitter @name'),
            'ikanakama'     => Yii::t('app', 'IKANAKAMA User ID'),
            'env'           => Yii::t('app', 'Capture Environment'),
            'blackout'      => Yii::t('app', 'Black out other players from the result image'),
        ];
    }
}
