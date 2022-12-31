<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

/**
 * This is the model class for table "battle_events".
 *
 * @property integer $id
 * @property string $events
 */
class BattleEvents extends \yii\db\ActiveRecord
{
    public function init()
    {
        $this->on(static::EVENT_AFTER_FIND, [$this, 'decodeEvents']);
        $this->on(static::EVENT_BEFORE_INSERT, [$this, 'encodeEvents']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'encodeEvents']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'events'], 'required'],
            [['id'], 'integer'],
            [['events'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'events' => 'Events',
        ];
    }

    public function encodeEvents()
    {
        if (substr($this->events, 0, 2) !== '[{') {
            return;
        }
        $encoded = 'gz' . base64_encode(gzencode($this->events, 9, FORCE_GZIP));
        if (strlen($this->events) > strlen($encoded)) {
            $this->events = $encoded;
        }
    }

    public function decodeEvents()
    {
        switch (substr($this->events, 0, 2)) {
            case '[{':
            default:
                break;

            case 'gz':
                $binary = base64_decode(substr($this->events, 2));
                $this->events = gzdecode($binary);
                break;
        }
    }
}
