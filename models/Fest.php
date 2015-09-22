<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;

/**
 * This is the model class for table "fest".
 *
 * @property integer $id
 * @property string $name
 * @property integer $start_at
 * @property integer $end_at
 *
 * @property OfficialData[] $officialDatas
 * @property OfficialResult $officialResult
 * @property Team[] $teams
 * @property Color[] $colors
 */
class Fest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start_at', 'end_at'], 'required'],
            [['name'], 'string'],
            [['start_at', 'end_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficialDatas()
    {
        return $this->hasMany(OfficialData::className(), ['fest_id' => 'id'])
            ->orderBy('official_data.downloaded_at ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficialResult()
    {
        return $this->hasOne(OfficialResult::className(), ['fest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['fest_id' => 'id']);
    }

    public function getAlphaTeam()
    {
        return $this->hasOne(Team::className(), ['fest_id' => 'id'])
            ->andWhere('team.color_id = 1');
    }

    public function getBravoTeam()
    {
        return $this->hasOne(Team::className(), ['fest_id' => 'id'])
            ->andWhere('team.color_id = 2');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('team', ['fest_id' => 'id']);
    }

    public function toJsonArray(DateTimeZone $tz = null)
    {
        $now = (int)(isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
        $alpha = $this->alphaTeam;
        $bravo = $this->bravoTeam;
        $officialResult = null;
        if ((int)$this->start_at > $now) {
            $state = 'scheduled';
        } elseif ((int)$this->end_at > $now) {
            $state = 'in session';
        } else {
            $state = 'closed';
            if ($this->officialResult) {
                $officialResult = [
                    'vote' => [
                        'alpha' => (int)$this->officialResult->alpha_people,
                        'bravo' => (int)$this->officialResult->bravo_people,
                        'multiply' => 1,
                    ],
                    'win' => [
                        'alpha' => (int)$this->officialResult->alpha_win,
                        'bravo' => (int)$this->officialResult->bravo_win,
                        'multiply' => (int)$this->officialResult->win_rate_times,
                    ],
                ];
            }
        }
        return [
            'id'    => (int)$this->id,
            'name'  => $this->name,
            'term'  => [
                'begin'         => (int)$this->start_at,
                'end'           => (int)$this->end_at,
                'begin_s'       => DateTimeFormatter::unixTimeToString((int)$this->start_at, $tz),
                'end_s'         => DateTimeFormatter::unixTimeToString((int)$this->end_at, $tz),
                'in_session'    => ($state === 'in session'),
                'status'        => $state,
            ],
            'teams' => [
                'alpha' => [
                    'name' => $alpha->name,
                    'ink' => $alpha->ink_color,
                ],
                'bravo' => [
                    'name' => $bravo->name,
                    'ink' => $bravo->ink_color,
                ],
            ],
            'result' => $officialResult,
        ];
    }
}
