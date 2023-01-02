<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use app\models\Battle;
use app\models\User;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

use function preg_replace;
use function trim;

class PatchBattleForm extends Model
{
    public const DELETE_MARK = '<<DELETE>>';

    // API
    public $apikey;
    public $test;

    public $id;
    public $link_url;
    public $note;
    public $private_note;

    public function rules()
    {
        return [
            [['apikey', 'id', 'link_url', 'note', 'private_note'], 'trim'],

            [['apikey', 'id'], 'required'],
            [['apikey'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'api_key'],
            [['test'], 'in', 'range' => ['validate', 'dry_run']],
            [['id'], 'exist',
                'targetClass' => Battle::class,
                'targetAttribute' => '{{battle}}.[[id]]',
                'filter' => function ($query) {
                    $query->innerJoinWith('user');
                    $query->andWhere(['{{user}}.[[api_key]]' => $this->apikey]);
                },
                'message' => 'Battle ID does not exist.',
            ],

            [['link_url'], 'url',
                'when' => fn ($model, $attr) => $model->$attr !== static::DELETE_MARK,
            ],
            [['link_url'], 'safe',
                'when' => fn ($model, $attr) => $model->$attr === static::DELETE_MARK,
            ],
            [['note', 'private_note'], 'string'],
            [['note', 'private_note'], 'filter', 'filter' => function ($value) {
                $value = (string)$value;
                $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value);
                $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value);
                $value = trim($value);
                return $value === '' ? null : $value;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function save(): Battle
    {
        $battle = Battle::find()
            ->innerJoinWith('user')
            ->andWhere([
                '{{battle}}.[[id]]' => $this->id,
                '{{user}}.[[api_key]]' => $this->apikey,
            ])
            ->orderBy(null)
            ->limit(1)
            ->one();
        if (!$battle) {
            throw new ServerErrorHttpException();
        }

        if ($this->link_url != '') {
            $battle->link_url = $this->link_url === static::DELETE_MARK
                ? null
                : $this->link_url;
        }

        $keys = ['note', 'private_note'];
        foreach ($keys as $key) {
            if ($this->$key != '') {
                $battle->$key = $this->$key === static::DELETE_MARK
                    ? null
                    : $this->$key;
            }
        }

        if ($battle->dirtyAttributes && $this->test !== 'dry_run') {
            if (!$battle->save()) {
                throw new ServerErrorHttpException();
            }
        }

        return $battle;
    }
}
