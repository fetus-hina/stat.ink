<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\validators\IdnToPunycodeFilterValidator;

class BattleForm extends Model
{
    public $lobby_id;
    public $rule_id;
    public $map_id;
    public $weapon_id;
    public $link_url;

    public function rules()
    {
        return [
            [['lobby_id'], 'exist',
                'targetClass' => Lobby::class,
                'targetAttribute' => 'id'],
            [['rule_id'], 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'id'],
            [['map_id'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'id'],
            [['weapon_id'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'id'],
            [['link_url'], 'url', 'enableIDN' => true],
            [['link_url'], IdnToPunycodeFilterValidator::class],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lobby_id'  => Yii::t('app', 'Lobby'),
            'rule_id'   => Yii::t('app', 'Mode'),
            'map_id'    => Yii::t('app', 'Stage'),
            'weapon_id' => Yii::t('app', 'Weapon'),
            'link_url'  => Yii::t('app', 'URL related to this battle'),
        ];
    }
}
