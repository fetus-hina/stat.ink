<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Expression;

final class m221224_021204_special3_rank extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $map = [
            'ultrashot' => 100,
            'greatbarrier' => 110,
            'shokuwander' => 120,
            'missile' => 130,
            'amefurashi' => 140,
            'nicedama' => 150,
            'hopsonar' => 160,
            'kyuinki' => 170,
            'megaphone51' => 180,
            'jetpack' => 190,
            'ultrahanko' => 200,
            'kanitank' => 210,
            'sameride' => 220,
            'tripletornado' => 230,
            'energystand' => 240,
        ];

        $this->addColumns('{{%special3}}', [
            'rank' => $this->integer()->null(),
        ]);

        $this->update('{{%special3}}', [
            'rank' => new Expression(
                vsprintf('(CASE %s %s END)', [
                    $db->quoteColumnName('key'),
                    implode(
                        ' ',
                        array_map(
                            fn (string $key, int $rank): string => vsprintf('WHEN %s THEN %d', [
                                $db->quoteValue($key),
                                $rank,
                            ]),
                            array_keys($map),
                            array_values($map),
                        ),
                    ),
                ]),
            ),
        ]);

        $this->alterColumn(
            '{{%special3}}',
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
        $this->dropColumn('{{%special3}}', 'rank');

        return true;
    }
}
