<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m231124_010248_subweapon_rank extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $ranks = [
            'splashbomb' => 100,
            'kyubanbomb' => 110,
            'quickbomb' => 120,
            'sprinkler' => 130,
            'splashshield' => 140,
            'tansanbomb' => 150,
            'curlingbomb' => 160,
            'robotbomb' => 170,
            'jumpbeacon' => 180,
            'pointsensor' => 190,
            'trap' => 200,
            'poisonmist' => 210,
            'linemarker' => 220,
            'torpedo' => 230,
        ];

        $this->addColumns('{{%subweapon3}}', [
            'rank' => $this->integer()->null(),
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('UPDATE %s SET rank = %s', [
                $db->quoteTableName('{{%subweapon3}}'),
                vsprintf('(CASE %s %s END)', [
                    $db->quoteColumnName('key'),
                    implode(
                        ' ',
                        array_map(
                            fn (string $key, int $value): string => vsprintf('WHEN %s THEN %s', [
                                $db->quoteValue($key),
                                (string)$db->quoteValue($value),
                            ]),
                            array_keys($ranks),
                            array_values($ranks),
                        ),
                    ),
                ]),
            ]),
        );

        $this->alterColumn(
            '{{%subweapon3}}',
            'rank',
            (string)$this->integer()->notNull()->unique(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%subweapon3}}', [
            'rank',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%subweapon3}}',
        ];
    }
}
