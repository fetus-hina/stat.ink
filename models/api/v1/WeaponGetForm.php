<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;

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
                'targetClass' => Weapon::className(),
                'targetAttribute' => 'key'],
            [['type'], 'exist',
                'targetClass' => WeaponType::className(),
                'targetAttribute' => 'key'],
            [['sub'], 'exist',
                'targetClass' => Subweapon::className(),
                'targetAttribute' => 'key'],
            [['special'], 'exist',
                'targetClass' => Special::className(),
                'targetAttribute' => 'key'],
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
