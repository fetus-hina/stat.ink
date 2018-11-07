<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

class Salmon2FilterForm extends Model
{
    public $user;

    public $stage;
    public $special;
    public $title;

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['stage', 'special', 'title'], 'string'],
            [['stage'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => 'key',
            ],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => 'key',
            ],
            [['title'], 'in',
                'range' => array_keys($this->getTitleList()),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage' => Yii::t('app', 'Stage'),
            'special' => Yii::t('app', 'Special'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    public function getTitleList(): array
    {
        return [
            'apprentice' => [
                'danger_rate' => [0.0, 20.0],
                'name' => Yii::t('app-salmon-title2', 'Apprentice'),
            ],
            'part_timer' => [
                'danger_rate' => [20.0, 40.0],
                'name' => Yii::t('app-salmon-title2', 'Part-Timer'),
            ],
            'go_getter' => [
                'danger_rate' => [40.0, 60.0],
                'name' => Yii::t('app-salmon-title2', 'Go-Getter'),
            ],
            'overachiever' => [
                'danger_rate' => [60.0, 80.0],
                'name' => Yii::t('app-salmon-title2', 'Overachiever'),
            ],
            'profreshional' => [
                'danger_rate' => [80.0, 999.9],
                'name' => Yii::t('app-salmon-title2', 'Profreshional'),
            ],
            'pro-0' => [
                'danger_rate' => [80.0, 100.0],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 0,
                    'to' => 100,
                ]),
            ],
            'pro-100' => [
                'danger_rate' => [100.0, 122.4],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 100,
                    'to' => 212,
                ]),
            ],
            'pro-212' => [
                'danger_rate' => [122.4, 137.6],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 212,
                    'to' => 288,
                ]),
            ],
            'pro-288' => [
                'danger_rate' => [137.6, 155.6],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 288,
                    'to' => 378,
                ]),
            ],
            'pro-413' => [
                'danger_rate' => [155.6, 177.8],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 378,
                    'to' => 489,
                ]),
            ],
            'pro-489' => [
                'danger_rate' => [177.8, 200.0],
                'name' => Yii::t('app-salmon-title2', 'Profreshional (Avg. {from}-{to})', [
                    'from' => 489,
                    'to' => 600,
                ]),
            ],
            'pro-600' => [
                'danger_rate' => [200.0, 999.9],
                'name' => Yii::t('app-salmon-title2', 'Hazard Level MAX!!'),
            ],
        ];
    }

    public function decorateQuery(ActiveQuery $query): ActiveQuery
    {
        if (!$this->validate()) {
            $query->andWhere('0 = 1');
            return $query;
        }

        if ($this->stage) {
            $stage = SalmonMap2::findOne(['key' => $this->stage]);
            $query->andWhere(['{{salmon2}}.[[stage_id]]' => $stage->id]);
        }

        if ($this->special) {
            $special = SalmonSpecial2::findOne(['key' => $this->special]);
            $query
                ->innerJoin(
                    'salmon_player2',
                    implode(' AND ', [
                        '{{salmon2}}.[[id]] = {{salmon_player2}}.[[work_id]]',
                        '{{salmon_player2}}.[[is_me]] = TRUE',
                    ])
                )
                ->andWhere([
                    '{{salmon_player2}}.[[special_id]]' => $special->id,
                ]);
        }

        if ($this->title) {
            $titles = $this->getTitleList();
            if ($info = $titles[$this->title] ?? null) {
                $query->andWhere(['and',
                    [
                        '>=',
                        '{{salmon2}}.[[danger_rate]]',
                        sprintf('%.1f', $info['danger_rate'][0]),
                    ],
                    [
                        '<',
                        '{{salmon2}}.[[danger_rate]]',
                        sprintf('%.1f', $info['danger_rate'][1]),
                    ],
                ]);
            }
        }

        return $query;
    }
}
