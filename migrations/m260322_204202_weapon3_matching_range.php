<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m260322_204202_weapon3_matching_range extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->addColumn(
            '{{%mainweapon3}}',
            'matching_range',
            (string)$this->decimal(3, 1)->null()->append('CHECK ([[matching_range]] > 0)'),
        );

        $data = $this->getData();
        $sql = vsprintf('UPDATE %s SET %s = %s', [
            $db->quoteTableName('{{%mainweapon3}}'),
            $db->quoteColumnName('matching_range'),
            vsprintf('(CASE %s %s ELSE NULL END)', [
                $db->quoteColumnName('key'),
                implode(
                    ' ',
                    array_map(
                        fn (string $key, int|float $value): string => sprintf(
                            'WHEN %s THEN %.1f',
                            $db->quoteValue($key),
                            (float)$value,
                        ),
                        array_keys($data),
                        array_values($data),
                    ),
                ),
            ]),
        ]);
        $this->execute($sql);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropColumn('{{%mainweapon3}}', 'matching_range');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%mainweapon3}}',
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function getData(): array
    {
        return [
            '52gal' => 13.3,
            '96gal' => 18,
            'bamboo14mk1' => 21,
            'barrelspinner' => 21,
            'bold' => 8.2,
            'bottlegeyser' => 20,
            'brella24mk1' => 14.5,
            'bucketslosher' => 14.5,
            'campingshelter' => 15,
            'carbon' => 9.5,
            'clashblaster' => 11,
            'dentalwiper_mint' => 15,
            'drivewiper' => 13,
            'dualsweeper' => 17,
            'dynamo' => 18.5,
            'examiner' => 17,
            'explosher' => 20.7,
            'fincent' => 13.1,
            'furo' => 15,
            'furuido' => 23,
            'gaen_ff' => 19.1,
            'h3reelgun' => 17,
            'hissen' => 11,
            'hokusai' => 10.5,
            'hotblaster' => 13.3,
            'hydra' => 24.5,
            'jetsweeper' => 22.5,
            'jimuwiper' => 16.5,
            'kelvin525' => 16,
            'kugelschreiber' => 24.5,
            'l3reelgun' => 13.5,
            'lact450' => 15,
            'liter4k' => 31,
            'liter4k_scope' => 33,
            'longblaster' => 17,
            'maneuver' => 12.5,
            'moprin' => 15.5,
            'nautilus47' => 18,
            'nova' => 11.2,
            'nzap85' => 12.5,
            'pablo' => 7,
            'parashelter' => 12.5,
            'prime' => 17,
            'promodeler_mg' => 11.3,
            'quadhopper_black' => 14,
            'rapid' => 16.7,
            'rapid_elite' => 19.2,
            'rpen_5h' => 26,
            'sblast92' => 16.5,
            'screwslosher' => 14.7,
            'sharp' => 12.1,
            'soytuber' => 21,
            'spaceshooter' => 16,
            'splatcharger' => 26,
            'splatroller' => 11.8,
            'splatscope' => 26.5,
            'splatspinner' => 15,
            'sputtery' => 9.8,
            'spygadget' => 11.8,
            'squiclean_a' => 18.5,
            'sshooter' => 12.9,
            'tristringer' => 25,
            'variableroller' => 14,
            'wakaba' => 11.3,
            'wideroller' => 12.5,
        ];
    }
}
