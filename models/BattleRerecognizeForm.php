<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\base\Model;

class BattleRerecognizeForm extends Model
{
    public $id;
    public $players;
    public $agent;
    public $agent_version;
    public $recognition_at;

    private $playersChanged = false;

    public function rules()
    {
        return [
            [['id'], 'exist',
                'targetClass' => Battle::class,
                'targetAttribute' => 'id',
            ],
            [['players'], 'validateArrayOfPlayers'],
            [['agent', 'agent_version'], 'string'],
            [['recognition_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'players' => 'Players',
        ];
    }

    public function validateArrayOfPlayers($attr, $params)
    {
        if (!is_array($this->$attr)) {
            $this->addError($attr, "{$attr} should be array");
            return;
        }

        if (!$this->$attr || count($this->$attr) > 8) {
            $this->addError($attr, "{$attr} size error");
            return;
        }

        foreach ($this->$attr as $i => $row) {
            $form = new BattleRerecognizePlayerForm();
            $form->attributes = $row;
            if (!$form->validate()) {
                foreach ($form->getFirstErrors() as $k => $v) {
                    $this->addError("{$attr}", "{$attr}.{$i} => {$k} : {$v}");
                }
                return;
            }
        }
    }

    public function getMyData()
    {
        foreach ($this->players as $p) {
            if ($p['is_me'] === 'yes' && $p['team'] === 'my') {
                return $p;
            }
        }
        return null;
    }

    public function getBattle()
    {
        return Battle::findOne(['id' => $this->id]);
    }

    public function save()
    {
        return $this->savePlayers() && $this->saveBattle();
    }

    protected function saveBattle()
    {
        $my = new BattleRerecognizePlayerForm();
        $my->attributes = $this->getMyData();
        $weapon = $my->weaponModel;
        $rank = $my->rank ? Rank::findOne(['key' => $my->rank]) : null;

        $battle = $this->getBattle();
        $battle->skipSaveHistory = true;
        $battle->attributes = [
            'weapon_id'     => $weapon->id ?? null,
            'level'         => $my->level,
            'rank_id'       => $rank->id ?? null,
            'rank_in_team'  => $my->rank_in_team,
            'kill'          => $my->kill,
            'death'         => $my->death,
            'my_point'      => $my->point,
        ];

        if (!$battle->dirtyAttributes && !$this->playersChanged) {
            return true;
        }

        $battle->ua_variables = json_encode(array_merge(
            @json_decode($battle->ua_variables, true) ?: [],
            [
                'rerecognized_agent' => sprintf('%s/%s', $this->agent ?: 'unknown', $this->agent_version ?: 'unknown'),
                'rerecognized_at' => gmdate('Y-m-d\TH:i:sP', $this->recognition_at),
            ]
        ));

        foreach (array_keys($battle->dirtyAttributes) as $k) {
            if ($k === 'weapon_id') {
                printf(
                    "[%d] %s = %s(%d) -> %s(%d)\n",
                    $this->id,
                    $k,
                    Weapon::findOne(['id' => $battle->getOldAttribute($k)])->key ?? null,
                    $battle->getOldAttribute($k),
                    Weapon::findOne(['id' => $battle->$k])->key ?? null,
                    $battle->$k
                );
            } else {
                printf(
                    "[%d]  %s = %s -> %s\n",
                    $this->id,
                    $k,
                    $battle->getOldAttribute($k),
                    $battle->$k
                );
            }
        }

        return $battle->save();
    }

    protected function savePlayers()
    {
        if (count($this->players) !== 8) {
            $count = BattlePlayer::find()->where(['battle_id' => $this->id])->count();
            if (count($this->players) != $count) {
                BattlePlayer::deleteAll([
                    'battle_id' => $this->id,
                ]);
            }
        }

        foreach ($this->players as $i => $player) {
            $form = new BattleRerecognizePlayerForm();
            $form->attributes = $player;

            $weapon = $form->weapon ? Weapon::findOne(['key' => $form->weapon]) : null;
            $rank = $form->rank ? Rank::findOne(['key' => $form->rank]) : null;
            $model = BattlePlayer::findOne([
                'battle_id' => $this->id,
                'is_my_team' => $form->team === 'my',
                'rank_in_team' => $form->rank_in_team,
            ]) ?? new BattlePlayer();
            $model->attributes = [
                'battle_id'     => $this->id,
                'is_my_team'    => $form->team === 'my',
                'is_me'         => $form->is_me === 'yes',
                'weapon_id'     => $weapon->id ?? null,
                'rank_id'       => $rank->id ?? null,
                'level'         => $form->level,
                'rank_in_team'  => $form->rank_in_team,
                'kill'          => $form->kill,
                'death'         => $form->death,
                'point'         => $form->point,
            ];

            if (!$model->dirtyAttributes) {
                continue;
            }

            foreach (array_keys($model->dirtyAttributes) as $k) {
                if ($k === 'weapon_id') {
                    printf(
                        "<%d:%d> %s = %s(%d) -> %s(%d)\n",
                        $this->id,
                        $i,
                        $k,
                        Weapon::findOne(['id' => $model->getOldAttribute($k)])->key ?? null,
                        $model->getOldAttribute($k),
                        Weapon::findOne(['id' => $model->$k])->key ?? null,
                        $model->$k
                    );
                } else {
                    printf(
                        "<%d:%d> %s = %s -> %s\n",
                        $this->id,
                        $i,
                        $k,
                        $model->getOldAttribute($k),
                        $model->$k
                    );
                }
            }
            if (!$model->save()) {
                return false;
            }
            $this->playersChanged = true;
        }
        return true;
    }
}
