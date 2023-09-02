<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\salmon3;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\SalmonTitle3;
use app\models\StatSalmon3Salmometer;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_keys;
use function array_map;
use function fwrite;
use function implode;
use function vsprintf;

use const STDERR;

trait SalmometerTrait
{
    protected function makeStatSalmon3Salmometer(): void
    {
        TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                function (Connection $db): void {
                    fwrite(STDERR, "delete old stat_salmon3_salmometer...\n");
                    $db->createCommand()
                        ->delete(StatSalmon3Salmometer::tableName())
                        ->execute();

                    fwrite(STDERR, "insert new stat_salmon3_salmometer...\n");
                    $db->createCommand($this->createInsertSqlToMakeStatSalmon3Salmometer($db))
                        ->execute();
                },
                Transaction::REPEATABLE_READ,
            );

        fwrite(STDERR, "vacuum analyze stat_salmon3_salmometer...\n");
        TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->createCommand(
                vsprintf('VACUUM (ANALYZE) %s', [
                    StatSalmon3Salmometer::tableName(),
                ]),
            )
            ->execute();

        fwrite(STDERR, "done.\n");
    }

    private function createInsertSqlToMakeStatSalmon3Salmometer(Connection $db): string
    {
        $title = TypeHelper::instanceOf(
            SalmonTitle3::find()
                ->andWhere(['key' => 'eggsecutive_vp'])
                ->limit(1)
                ->one(),
            SalmonTitle3::class,
        );

        $select = (new Query())
            ->select([
                'king_smell' => '{{%salmon3}}.[[king_smell]]',
                'jobs' => 'COUNT(*)',
                'cleared' => vsprintf('SUM(%s)', [
                    vsprintf('CASE %s END', [
                        implode(' ', [
                            vsprintf('WHEN %s.%s = 3 THEN 1', [
                                $db->quoteTableName('{{%salmon3}}'),
                                $db->quoteColumnName('clear_waves'),
                            ]),
                            'ELSE 0',
                        ]),
                    ]),
                ]),
            ])
            ->from('{{%salmon3}}')
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[has_broken_data]]' => false,
                    '{{%salmon3}}.[[has_disconnect]]' => false,
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_big_run]]' => false,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[title_before_id]]' => $title->id,
                ],
                ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
                ['not', ['{{%salmon3}}.[[king_smell]]' => null]],
            ])
            ->groupBy(['{{%salmon3}}.[[king_smell]]']);

        return vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName(StatSalmon3Salmometer::tableName()),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
    }
}
