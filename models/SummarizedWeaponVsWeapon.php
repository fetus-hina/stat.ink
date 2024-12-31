<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

use function array_map;
use function usort;

use const NAN;

class SummarizedWeaponVsWeapon extends Model
{
    public $lhs_weapon_id;
    public $rhs_weapon_id;
    public $battle_count;
    public $win_count;

    public $lhsWeapon = false;
    public $rhsWeapon = false;

    public static function find(
        int $weapon_id,
        $rule_id = null,
        $version_id = null,
    ): array {
        $query = (new Query())
            ->select([
                'wid1' => 'weapon_id_1',
                'wid2' => 'weapon_id_2',
                'battle_count' => 'SUM([[battle_count]])',
                'win_count' => 'SUM([[win_count]])',
            ])
            ->from(StatWeaponVsWeapon::tableName())
            ->andWhere(['or',
                ['weapon_id_1' => $weapon_id],
                ['weapon_id_2' => $weapon_id],
            ])
            ->groupBy(['weapon_id_1', 'weapon_id_2'])
            ->having(['>=', 'SUM([[battle_count]])', 1]);
        if ($rule_id) {
            $query->andWhere(['rule_id' => $rule_id]);
        }
        if ($version_id) {
            $query->andWhere(['version_id' => $version_id]);
        }

        $weapons = static::getAllWeapons();
        $result = array_map(
            function ($row) use ($weapon_id, $weapons) {
                $o = Yii::createObject(['class' => static::class]);
                if ($weapon_id == $row['wid1']) {
                    $o->lhs_weapon_id = (int)$row['wid1'];
                    $o->rhs_weapon_id = (int)$row['wid2'];
                    $o->win_count = (int)$row['win_count'];
                    $o->battle_count = (int)$row['battle_count'];
                } else {
                    $o->lhs_weapon_id = (int)$row['wid2'];
                    $o->rhs_weapon_id = (int)$row['wid1'];
                    $o->win_count = (int)$row['battle_count'] - (int)$row['win_count'];
                    $o->battle_count = (int)$row['battle_count'];
                }
                $o->lhsWeapon = $weapons[$o->lhs_weapon_id] ?? null;
                $o->rhsWeapon = $weapons[$o->rhs_weapon_id] ?? null;
                return $o;
            },
            $query->all(),
        );
        usort($result, fn ($a, $b) => ($b->winPct <=> $a->winPct));
        return $result;
    }

    // イーガーローディングもどきとして全ブキリストを取得する
    private static function getAllWeapons(): array
    {
        $list = Weapon::find()
            ->with(['special', 'subweapon', 'type'])
            ->all();
        $ret = [];
        foreach ($list as $weapon) {
            $ret[$weapon->id] = $weapon;
        }
        return $ret;
    }

    public function getWinPct(): float
    {
        return $this->battle_count < 1
            ? NAN
            : (100 * $this->win_count / $this->battle_count);
    }

    public function getLhsWeapon(): ?Weapon
    {
        if ($this->lhsWeapon === false) {
            $this->lhsWeapon = Weapon::findOne(['id' => $this->lhs_weapon_id]);
        }
        return $this->lhsWeapon;
    }

    public function getRhsWeapon(): ?Weapon
    {
        if ($this->rhsWeapon === false) {
            $this->rhsWeapon = Weapon::findOne(['id' => $this->rhs_weapon_id]);
        }
        return $this->rhsWeapon;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lhs_weapon_id', 'rhs_weapon_id', 'battle_count', 'win_count'], 'required'],
            [['lhs_weapon_id', 'rhs_weapon_id', 'battle_count', 'win_count'], 'integer'],
            [['lhs_weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
                'targetAttribute' => ['weapon_id_1' => 'id'],
            ],
            [['rhs_weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
                'targetAttribute' => ['weapon_id_2' => 'id'],
            ],
        ];
    }
}
