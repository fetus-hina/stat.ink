<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\salmon3;

use Yii;
use app\models\StatSalmon3TideEvent;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use app\models\StatSalmon3MapKing;

use function array_map;
use function assert;
use function implode;
use function vsprintf;

trait MapKingTrait
{
    protected function makeStatSalmon3MapKing(): void
    {
        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $db->transaction(
            function (Connection $db): void {
                $this->updateStatSalmon3MapKing($db);
            },
            Transaction::READ_COMMITTED,
        );

        fwrite(STDERR, "Vacuum analyze stat_salmon3_map_king...\n");
        $db->createCommand('VACUUM (ANALYZE) {{%stat_salmon3_map_king}}')
            ->execute();

        fwrite(STDERR, "Done.\n");
    }

    private function updateStatSalmon3MapKing(Connection $db): void
    {
        fwrite(STDERR, "Delete old stat_salmon3_map_king...\n");
        StatSalmon3MapKing::deleteAll();

        fwrite(STDERR, "Update stat_salmon3_map_king...\n");
        $select = (new \yii\db\Query())
            ->select([
                'map_id' => '{{%salmon3}}.[[stage_id]]',
                'big_map_id' => '{{%salmon3}}.[[big_stage_id]]',
                'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                'jobs' => 'COUNT(*)',
                'cleared' => 'SUM(CASE WHEN {{%salmon3}}.[[clear_extra]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[clear_waves]]' => 3,
                    '{{%salmon3}}.[[has_broken_data]]' => false,
                    '{{%salmon3}}.[[has_disconnect]]' => false,
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[clear_extra]]' => null]],
                ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                ['or',
                    ['and',
                        ['not', ['{{%salmon3}}.[[stage_id]]' => null]],
                        ['{{%salmon3}}.[[big_stage_id]]' => null],
                        ['{{%salmon3}}.[[is_big_run]]' => false],
                    ],
                    ['and',
                        ['not', ['{{%salmon3}}.[[big_stage_id]]' => null]],
                        ['{{%salmon3}}.[[stage_id]]' => null],
                        ['{{%salmon3}}.[[is_big_run]]' => true],
                    ],
                ],
            ])
            ->groupBy([
                '{{%salmon3}}.[[stage_id]]',
                '{{%salmon3}}.[[big_stage_id]]',
                '{{%salmon3}}.[[king_salmonid_id]]',
            ]);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            '{{%stat_salmon3_map_king}}',
            implode(', ', array_map(
                $db->quoteColumnName(...),
                array_keys($select->select),
            )),
            $select->createCommand($db)->rawSql,
        ]);

        $db->createCommand($sql)->execute();
    }
}
