<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\validators\IdnToPunycodeFilterValidator;
use yii\base\Model;

use function preg_replace;
use function trim;

class BattleForm extends Model
{
    public $lobby_id;
    public $rule_id;
    public $map_id;
    public $weapon_id;
    public $link_url;
    public $note;
    public $private_note;

    public function rules()
    {
        return [
            [['lobby_id', 'rule_id', 'map_id', 'weapon_id', 'link_url', 'note', 'private_note'], 'filter',
                'filter' => function ($value) {
                    $value = trim((string)$value);
                    return $value === '' ? null : $value;
                },
            ],
            [['lobby_id'], 'exist',
                'targetClass' => Lobby::class,
                'targetAttribute' => 'id',
            ],
            [['rule_id'], 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'id',
            ],
            [['map_id'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'id',
            ],
            [['weapon_id'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'id',
            ],
            [['link_url'], 'url', 'enableIDN' => true],
            [['link_url'], IdnToPunycodeFilterValidator::class],
            [['note', 'private_note'], 'string'],
            [['link_url', 'note', 'private_note'], 'filter', 'filter' => function ($value) {
                $value = (string)$value;
                $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value);
                $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value);
                $value = trim($value);
                return $value === '' ? null : $value;
            },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lobby_id' => Yii::t('app', 'Lobby'),
            'rule_id' => Yii::t('app', 'Mode'),
            'map_id' => Yii::t('app', 'Stage'),
            'weapon_id' => Yii::t('app', 'Weapon'),
            'link_url' => Yii::t('app', 'URL related to this battle'),
            'note' => Yii::t('app', 'Note (public)'),
            'private_note' => Yii::t('app', 'Note (private)'),
        ];
    }
}
