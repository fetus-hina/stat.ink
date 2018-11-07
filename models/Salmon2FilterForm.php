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

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['stage', 'special'], 'string'],
            [['stage'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => 'key',
            ],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => 'key',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage' => Yii::t('app', 'Stage'),
            'special' => Yii::t('app', 'Special'),
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

        return $query;
    }
}
