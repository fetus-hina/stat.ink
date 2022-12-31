<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lobby_group3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 * @property integer $importance
 *
 * @property Lobby3[] $lobby3s
 */
class LobbyGroup3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'lobby_group3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank', 'importance'], 'required'],
            [['rank', 'importance'], 'default', 'value' => null],
            [['rank', 'importance'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['importance'], 'unique'],
            [['key'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
            'importance' => 'Importance',
        ];
    }

    public function getLobby3s(): ActiveQuery
    {
        return $this->hasMany(Lobby3::class, ['group_id' => 'id']);
    }
}
