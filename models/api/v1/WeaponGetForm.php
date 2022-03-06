<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use yii\base\Model;
use yii\db\ActiveQuery;

class WeaponGetForm extends Model
{
    public $weapon;
    public $type;
    public $sub;
    public $special;

    public function rules()
    {
        return [
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
            ],
            [['type'], 'exist',
                'targetClass' => WeaponType::class,
                'targetAttribute' => 'key',
            ],
            [['sub'], 'exist',
                'targetClass' => Subweapon::class,
                'targetAttribute' => 'key',
            ],
            [['special'], 'exist',
                'targetClass' => Special::class,
                'targetAttribute' => 'key',
            ],
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

    public function filterQuery(ActiveQuery $query)
    {
        if ($this->weapon) {
            $query->andWhere(['{{weapon}}.[[key]]' => $this->weapon]);
        }
        if ($this->type) {
            $query->innerJoinWith('type');
            $query->andWhere(['{{weapon_type}}.[[key]]' => $this->type]);
        }
        if ($this->sub) {
            $query->innerJoinWith('subweapon');
            $query->andWhere(['{{subweapon}}.[[key]]' => $this->sub]);
        }
        if ($this->special) {
            $query->innerJoinWith('special');
            $query->andWhere(['{{special}}.[[key]]' => $this->special]);
        }
        return $query;
    }
}
