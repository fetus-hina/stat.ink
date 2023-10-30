<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression as DbExpr;

class m181206_132203_salmon_map_short extends Migration
{
    public function up()
    {
        $names = $this->getShortNames();
        $this->addColumn('salmon_map2', 'short_name', $this->string(32));
        $this->update(
            'salmon_map2',
            [
                'short_name' => new DbExpr(
                    vsprintf('(CASE %s %s END)', [
                        $this->db->quoteColumnName('key'),
                        implode(' ', array_map(
                            fn (string $key, string $name): string => vsprintf('WHEN %s THEN %s', [
                                $this->db->quoteValue($key),
                                $this->db->quoteValue($name),
                            ]),
                            array_keys($names),
                            array_values($names),
                        )),
                    ]),
                ),
            ],
            ['key' => array_keys($names)],
        );
        $this->execute('ALTER TABLE {{salmon_map2}} ALTER COLUMN [[short_name]] SET NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('salmon_map2', 'short_name');
    }

    private function getShortNames(): array
    {
        return [
            'dam' => 'Grounds',
            'donburako' => 'Bay',
            'shaketoba' => 'Outpost',
            'tokishirazu' => 'Smokeyard',
            'polaris' => 'Polaris',
        ];
    }
}
