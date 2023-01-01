<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function array_filter;
use function array_shift;
use function ceil;
use function min;
use function usort;
use function version_compare;

/**
 * This is the model class for table "weapon_attack".
 *
 * @property integer $id
 * @property integer $main_weapon_id
 * @property integer $version_id
 * @property string $damage
 *
 * @property SplatoonVersion $version
 * @property Weapon $mainWeapon
 */
class WeaponAttack extends ActiveRecord
{
    public static function findByWeaponAndVersion(Weapon $weapon, SplatoonVersion $version)
    {
        // 当該ブキのデータを全部取り寄せる(どうせ大した量ではない)
        $list = static::find()
            ->with('version')
            ->andWhere(['{{weapon_attack}}.[[main_weapon_id]]' => $weapon->main_group_id])
            ->all();

        // 指定バージョンより先のバージョンは捨てる
        $list = array_filter($list, fn ($target) => $target->version && version_compare($target->version->tag, $version->tag, '<='));

        // 新しい順に並び替える
        usort($list, fn ($a, $b) => version_compare($b->version->tag, $a->version->tag));

        // 最初の要素が目的の代物
        return empty($list) ? null : array_shift($list);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon_attack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['main_weapon_id', 'damage'], 'required'],
            [['main_weapon_id', 'version_id'], 'integer'],
            [['damage'], 'number'],
            [['main_weapon_id', 'version_id'], 'unique',
                'targetAttribute' => ['main_weapon_id', 'version_id'],
                'message' => 'The combination of Main Weapon ID and Version ID has already been taken.'],
            [['version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion::class,
                'targetAttribute' => ['version_id' => 'id']],
            [['main_weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
                'targetAttribute' => ['main_weapon_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'main_weapon_id' => 'Main Weapon ID',
            'version_id' => 'Version ID',
            'damage' => 'Damage',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(SplatoonVersion::class, ['id' => 'version_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMainWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'main_weapon_id']);
    }

    public function getHitToKill()
    {
        return ceil(100 / $this->damage);
    }

    public function getDamageCap()
    {
        switch ($this->getHitToKill()) {
            case 1:
                return 999.9;
            case 2:
                return 99.9;
            case 3:
                return 49.9;
            case 4:
                return 33.3;
            case 5:
                return 24.9;
        }
    }

    public function getVirtualDamage($ratio)
    {
        return $this->damage * $ratio;
    }

    public function getRealDamage($ratio)
    {
        $virtual = $this->getVirtualDamage($ratio);
        $limit = 100 / $this->getHitToKill();
        return min($virtual, $limit);
    }
}
