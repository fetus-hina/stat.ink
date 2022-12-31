<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181005_104443_salmon_special extends Migration
{
    public function up()
    {
        $this->createTable('salmon_special2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
            'splatnet' => $this->integer(),
            'special_id' => $this->pkRef('special2'),
        ]);

        $data = [
            // key => splatnet
            'chakuchi' => 9,
            'jetpack' => 8,
            'pitcher' => 2,
            'presser' => 7,
        ];
        $keys = implode(', ', array_map(
            function (string $key): string {
                return $this->db->quoteValue($key);
            },
            array_keys($data),
        ));
        $splatnet = sprintf(
            'CASE %s %s END',
            $this->db->quoteColumnName('key'),
            implode(' ', array_map(
                function (string $key, int $splatnet): string {
                    return sprintf(
                        'WHEN %s THEN %d',
                        $this->db->quoteValue($key),
                        $splatnet,
                    );
                },
                array_keys($data),
                array_values($data),
            )),
        );
        $name = sprintf(
            'CASE %s %s END',
            $this->db->quoteColumnName('key'),
            implode(' ', [
                sprintf(
                    'WHEN %s THEN %s',
                    $this->db->quoteValue('pitcher'),
                    $this->db->quoteValue('Splat-Bomb Launcher'),
                ),
                'ELSE ' . $this->db->quoteColumnName('name'),
            ]),
        );
        $this->execute(
            'INSERT INTO {{salmon_special2}}([[key]], [[name]], [[splatnet]], [[special_id]]) ' .
            "SELECT [[key]], {$name}, {$splatnet}, [[id]] " .
            'FROM {{special2}} ' .
            "WHERE {{key}} IN ({$keys}) " .
            'ORDER BY [[name]] ASC',
        );
    }

    public function down()
    {
        $this->dropTable('salmon_special2');
    }
}
