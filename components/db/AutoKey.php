<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use function array_keys;
use function array_values;
use function preg_replace;
use function rtrim;
use function str_replace;
use function strtolower;
use function substr;
use function trim;

trait AutoKey
{
    protected static function name2key(string $name): string
    {
        $table = [
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
        ];
        $name = str_replace(array_keys($table), array_values($table), $name);
        $name = preg_replace('/[^A-Za-z0-9]+/', '_', $name);
        $name = trim($name, '_');
        return strtolower($name);
    }

    protected static function name2key3(string $name, int $length = 32): string
    {
        return rtrim(substr(self::name2key($name), 0, $length), '_');
    }
}
