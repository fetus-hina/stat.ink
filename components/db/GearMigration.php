<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

namespace app\components\db;

use DateTimeInterface;
use app\components\helpers\Battle as BattleHelper;
use yii\db\Expression;
use yii\db\Query;

trait GearMigration
{
    protected function upGear2(
        string $key,
        string $name,
        string $type,
        string $brand,
        ?string $ability,
        ?int $splatnet
    ) : void {
        $this->insert('gear2', [
            'key'           => $key,
            'name'          => $name,
            'type_id'       => $this->findId('gear_type', $type),
            'brand_id'      => $this->findId('brand2', $brand),
            'ability_id'    => $ability ? $this->findId('ability2', $ability) : null,
            'splatnet'      => $splatnet,
        ]);
    }

    protected function downGear2(string $key) : void
    {
        $this->delete('gear2', ['key' => $key]);
    }

    protected static function salmonGear2(string $name, string $type, ?int $splatnet): array
    {
        return [
            static::name2key($name),
            $name,
            $type,
            'grizzco',
            null,
            $splatnet,
        ];
    }

    protected static function name2key(string $name) : string
    {
        $table = [
            // {{{
            '&' => ' and ',
            "'" => '',
            'Α' => ' alpha ',
            'α' => ' alpha ',
            'Β' => ' beta ',
            'β' => ' beta ',
            'Γ' => ' gamma ',
            'γ' => ' gamma ',
            'Δ' => ' delta ',
            'δ' => ' delta ',
            'Ε' => ' epsilon ',
            'ε' => ' epsilon ',
            'Ζ' => ' zeta ',
            'ζ' => ' zeta ',
            'Η' => ' eta ',
            'η' => ' eta ',
            'Θ' => ' theta ',
            'θ' => ' theta ',
            'Ι' => ' iota ',
            'ι' => ' iota ',
            'Κ' => ' kappa ',
            'κ' => ' kappa ',
            'Λ' => ' lambda ',
            'λ' => ' lambda ',
            'Μ' => ' mu ',
            'μ' => ' mu ',
            'Ν' => ' nu ',
            'ν' => ' nu ',
            'Ξ' => ' xi ',
            'ξ' => ' xi ',
            'Ο' => ' omicron ',
            'ο' => ' omicron ',
            'Π' => ' pi ',
            'π' => ' pi ',
            'Ρ' => ' rho ',
            'ρ' => ' rho ',
            'Σ' => ' sigma ',
            'σ' => ' sigma ',
            'ς' => ' sigma ',
            'Τ' => ' tau ',
            'τ' => ' tau ',
            'Υ' => ' upsilon ',
            'υ' => ' upsilon ',
            'Φ' => ' phi ',
            'φ' => ' phi ',
            'Χ' => ' chi ',
            'χ' => ' chi ',
            'Ψ' => ' psi ',
            'ψ' => ' psi ',
            'Ω' => ' omega ',
            'ω' => ' omega ',
            // }}}
        ];
        $name = str_replace(array_keys($table), array_values($table), $name);
        $name = preg_replace('/[^A-Za-z0-9]+/', '_', $name);
        $name = trim($name, '_');
        return strtolower($name);
    }

    private function findId(string $table, string $tag) : ?int
    {
        $id = (new Query())
            ->select('id')
            ->from($table)
            ->where(['key' => $tag])
            ->limit(1)
            ->scalar();
        return $id ? (int)$id : null;
    }
}
