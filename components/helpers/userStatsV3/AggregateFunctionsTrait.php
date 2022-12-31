<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userStatsV3;

use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use app\models\Lobby3;

use const SORT_ASC;
use const SORT_DESC;

trait AggregateFunctionsTrait
{
    /**
     * @param string[] $additionalConds
     */
    protected static function statsSum(string $valueExpr, array $additionalConds = []): string
    {
        return self::statsAggregate('SUM', $valueExpr, $additionalConds);
    }

    /**
     * @param string[] $additionalConds
     */
    protected static function statsMax(string $valueExpr, array $additionalConds = []): string
    {
        return self::statsAggregate('MAX', $valueExpr, $additionalConds);
    }

    /**
     * @param string[] $additionalConds
     */
    protected static function statsAggregate(
        string $func,
        string $valueExpr,
        array $additionalConds = [],
    ): string {
        $positive = fn (string $column): string => \vsprintf(
            '(%1$s IS NOT NULL) AND (%1$s >= 0)',
            [
                $column,
            ],
        );

        $conds = \array_merge(
            [
                '{{b}}.[[result_id]] IS NOT NULL',
                '{{r}}.[[aggregatable]] = TRUE',
                $positive('{{b}}.[[kill]]'),
                $positive('{{b}}.[[death]]'),
                $positive('{{b}}.[[assist]]'),
                $positive('{{b}}.[[special]]'),
                $positive('{{b}}.[[inked]]'),
                '{{b}}.[[start_at]] IS NOT NULL',
                '{{b}}.[[end_at]] IS NOT NULL',
                '{{b}}.[[start_at]] < {{b}}.[[end_at]]',
                '{{b}}.[[has_disconnect]] = FALSE',
            ],
            $additionalConds,
        );
        return \vsprintf('%1$s(CASE WHEN %3$s THEN %2$s ELSE 0 END)', [
            $func,
            $valueExpr,
            \implode(
                ' AND ',
                \array_map(
                    fn (string $cond): string => \sprintf('(%s)', $cond),
                    $conds,
                ),
            ),
        ]);
    }

    protected static function statsTimestamp(string $column): string
    {
        return \sprintf('EXTRACT(EPOCH FROM %s)', $column);
    }

    /**
     * @return string[]
     */
    protected static function statsCondBankara(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = self::statsCondLobby('bankara_%', true);
        }
        return $cache;
    }

    /**
     * @return string[]
     */
    protected static function statsCondSplatfestChallenge(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = self::statsCondLobby('splatfest_challenge', false);
        }
        return $cache;
    }

    /**
     * @return string[]
     */
    protected static function statsCondLobby(string $lobby, bool $like = false): array
    {
        $list = Lobby3::find()
            ->andWhere(
                $like
                    ? ['like', 'key', $lobby, false]
                    : ['key' => $lobby],
            )
            ->all();

        if (!$list) {
            throw new LogicException();
        }

        return [
            \vsprintf('{{b}}.[[lobby_id]] IN (%s)', [
                \implode(
                    ', ',
                    \array_map(
                        fn (Lobby3 $model): string => (string)$model->id,
                        $list,
                    ),
                ),
            ]),
        ];
    }

    protected static function periodToSeason(int $period): ?string
    {
        $date = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTimestamp($period * 7200);
        switch ((int)$date->format('n')) {
            case 1:
            case 2:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y') - 1, 12, 1);

            case 3:
            case 4:
            case 5:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 3, 1);

            case 6:
            case 7:
            case 8:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 6, 1);

            case 9:
            case 10:
            case 11:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 9, 1);

            case 12:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 12, 1);

            default:
                throw new LogicException();
        }
    }
}
