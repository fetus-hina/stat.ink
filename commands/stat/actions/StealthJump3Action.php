<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands\stat\actions;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use Throwable;
use Yii;
use app\components\helpers\Battle3Helper;
use app\components\helpers\TypeHelper;
use app\models\Season3;
use app\models\StatStealthJumpEquipment3;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_keys;
use function array_map;
use function fprintf;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const STDERR;

final class StealthJump3Action extends Action
{
    public function run(): int
    {
        $isOk = TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                $this->makeStats(...),
                Transaction::REPEATABLE_READ,
            );
        if (!$isOk) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->vacuumTables(
            TypeHelper::instanceOf(Yii::$app->db, Connection::class),
        );

        return ExitCode::OK;
    }

    private function makeStats(Connection $db): bool
    {
        try {
            fwrite(STDERR, "Updating Stealth Jump usage\n");

            if (!$seasons = $this->getTargetSeasons($db)) {
                fwrite(STDERR, "No target seasons\n");
                return true;
            }

            StatStealthJumpEquipment3::deleteAll([
                'season_id' => ArrayHelper::getColumn($seasons, 'id'),
            ]);

            $query = new Query()
                ->select([
                    'season_id' => '{{%season3}}.[[id]]',
                    'rule_id' => '{{%rule3}}.[[id]]',
                    'x_power' => '(FLOOR({{%battle3}}.[[x_power_after]] / 50.0) * 50.0)',
                    'players' => 'COUNT(*)',
                    'equipments' => vsprintf('SUM(CASE %s END)', [
                        implode(' ', [
                            vsprintf('WHEN %s.%s = %s THEN 1', [
                                $db->quoteTableName('{{%ability3}}'),
                                $db->quoteColumnName('key'),
                                $db->quoteValue('stealth_jump'),
                            ]),
                            'ELSE 0',
                        ]),
                    ]),
                ])
                ->from('{{%battle3}}')
                ->innerJoin(
                    '{{%season3}}',
                    '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]',
                )
                ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                ->innerJoin('{{%rule_group3}}', '{{%rule3}}.[[group_id]] = {{%rule_group3}}.[[id]]')
                ->innerJoin(
                    '{{%battle_player3}}',
                    '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]',
                )
                ->innerJoin(
                    ['headgear' => '{{%gear_configuration3}}'],
                    '{{%battle_player3}}.[[headgear_id]] = {{%headgear}}.[[id]]',
                )
                ->innerJoin(
                    ['clothing' => '{{%gear_configuration3}}'],
                    '{{%battle_player3}}.[[clothing_id]] = {{%clothing}}.[[id]]',
                )
                ->innerJoin(
                    ['shoes' => '{{%gear_configuration3}}'],
                    '{{%battle_player3}}.[[shoes_id]] = {{%shoes}}.[[id]]',
                )
                ->innerJoin('{{%ability3}}', '{{%shoes}}.[[ability_id]] = {{%ability3}}.[[id]]')
                ->andWhere(['and',
                    [
                        '{{%battle3}}.[[has_disconnect]]' => false,
                        '{{%battle3}}.[[is_automated]]' => true,
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%battle3}}.[[use_for_entire]]' => true,
                        '{{%battle_player3}}.[[is_me]]' => false,
                        '{{%lobby3}}.[[key]]' => 'xmatch',
                        '{{%rule_group3}}.[[key]]' => 'gachi',
                        '{{%season3}}.[[id]]' => ArrayHelper::getColumn($seasons, 'id'),
                    ],
                    ['not', ['{{%battle3}}.[[x_power_after]]' => null]],
                    ['not', ['{{%clothing}}.[[ability_id]]' => null]],
                    ['not', ['{{%headgear}}.[[ability_id]]' => null]],
                    ['not', ['{{%shoes}}.[[ability_id]]' => null]],
                ])
                ->groupBy([
                    '{{%season3}}.[[id]]',
                    '{{%rule3}}.[[id]]',
                    'FLOOR({{%battle3}}.[[x_power_after]] / 50.0)',
                ])
                ->orderBy([
                    'season_id' => SORT_ASC,
                    'rule_id' => SORT_ASC,
                    'x_power' => SORT_ASC,
                ]);

            $sql = vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName(StatStealthJumpEquipment3::tableName()),
                implode(', ', array_map(
                    $db->quoteColumnName(...),
                    array_keys($query->select ?? throw new LogicException('No columns selected')),
                )),
                $query->createCommand($db)->rawSql,
            ]);

            $db->createCommand($sql)->execute();

            return true;
        } catch (Throwable $e) {
            fprintf(STDERR, "Error: %s\n", $e->getMessage());
            TypeHelper::instanceOf($db->transaction, Transaction::class)->rollBack();
            return false;
        }
    }

    /**
     * @return Season3[]
     */
    private function getTargetSeasons(Connection $db): array
    {
        $startDay = new DateTimeImmutable()
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->modify('midnight')
            ->modify(sprintf('%d days ago', Battle3Helper::ENTIRE_STATS_RECALC_DAYS));

        $endDay = new DateTimeImmutable()
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->modify('today');

        return Season3::find()
            ->andWhere(['&&', 'term',
                new Expression(
                    vsprintf("TSTZRANGE(%s, %s, '[]')", [
                        $db->quoteValue($startDay->format(DateTimeInterface::ATOM)),
                        $db->quoteValue($endDay->format(DateTimeInterface::ATOM)),
                    ]),
                ),
            ])
            ->all($db);
    }

    private function vacuumTables(Connection $db): void
    {
        $tables = [
            StatStealthJumpEquipment3::tableName(),
        ];

        foreach ($tables as $table) {
            fprintf(STDERR, "Vacuuming %s\n", $table);
            $db->createCommand(
                vsprintf('VACUUM ( ANALYZE ) %s', [
                    $db->quoteTableName($table),
                ]),
            )->execute();
        }

        fwrite(STDERR, "OK\n");
    }
}
