<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\dlStats3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

use function array_map;
use function array_merge;
use function array_slice;
use function count;
use function dirname;
use function fclose;
use function file_exists;
use function fopen;
use function fprintf;
use function fseek;
use function fwrite;
use function gmdate;
use function implode;
use function stream_copy_to_stream;
use function strtotime;
use function tmpfile;
use function unlink;
use function vfprintf;

use const SEEK_SET;
use const SORT_ASC;
use const STDERR;

trait BattleTrait
{
    use CsvUtilTrait;
    use DateUtilTrait;
    use ModelUtilTrait;
    use ZipUtilTrait;

    public function actionCreateBattleResultsCsv(): int
    {
        $allSuccess = true;

        $aDay = new DateInterval('P1D');
        $sDay = self::startDay();
        $eDay = self::today();
        for ($date = $sDay; $date < $eDay; $date = $date->add($aDay)) {
            fprintf(STDERR, "%s - %s\n", __METHOD__, $date->format('Y-m-d'));

            $file = implode('/', [
                Yii::getAlias(self::BASE_BATTLE_RESULTS_CSV),
                $date->format('Y'),
                $date->format('m'),
                $date->format('Y-m-d') . '.csv',
            ]);

            if (file_exists($file)) {
                fwrite(STDERR, "  => exists\n");
                continue;
            }

            fwrite(STDERR, "  => creating CSV\n");
            if (!$this->createBattleResultsCsv($date, $file)) {
                $allSuccess = false;
            }
        }

        if (!$this->createBattleResultsCsvZip()) {
            $allSuccess = false;
        }

        return $allSuccess ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    private function createBattleResultsCsv(DateTimeImmutable $date, string $outPath): bool
    {
        $header = false;
        if (!$fh = @tmpfile()) {
            fwrite(STDERR, "tmpfile() failed\n");

            return false;
        }

        try {
            $playerColumns = fn (string $prefix): array => array_map(
                fn (string $column) => "{$prefix}-{$column}",
                [
                    'weapon',
                    'kill-assist',
                    'kill',
                    'assist',
                    'death',
                    'special',
                    'inked',
                    'abilities',
                ],
            );

            $notNull = fn (string $identifier): array => ['not', [$identifier => null]];
            $query = Battle3::find()
                ->innerJoinWith([
                    'lobby',
                    'map',
                    'result',
                    'rule',
                    'version',
                ])
                ->with([
                    'battlePlayer3s',
                    'battlePlayer3s.clothing',
                    'battlePlayer3s.clothing.ability',
                    'battlePlayer3s.clothing.gearConfigurationSecondary3s.ability',
                    'battlePlayer3s.headgear',
                    'battlePlayer3s.headgear.ability',
                    'battlePlayer3s.headgear.gearConfigurationSecondary3s.ability',
                    'battlePlayer3s.shoes',
                    'battlePlayer3s.shoes.ability',
                    'battlePlayer3s.shoes.gearConfigurationSecondary3s.ability',
                    'battlePlayer3s.weapon',
                    'event',
                    'medals',
                    'medals.canonical',
                    'ourTeamTheme',
                    'rankBefore',
                    'theirTeamTheme',
                ])
                ->andWhere(['and',
                    $notNull('{{%battle3}}.[[end_at]]'),
                    $notNull('{{%battle3}}.[[lobby_id]]'),
                    $notNull('{{%battle3}}.[[map_id]]'),
                    $notNull('{{%battle3}}.[[period]]'),
                    $notNull('{{%battle3}}.[[result_id]]'),
                    $notNull('{{%battle3}}.[[rule_id]]'),
                    $notNull('{{%battle3}}.[[start_at]]'),
                    $notNull('{{%battle3}}.[[weapon_id]]'),
                    [
                        '{{%battle3}}.[[has_disconnect]]' => false,
                        '{{%battle3}}.[[is_automated]]' => true,
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%battle3}}.[[use_for_entire]]' => true,
                        '{{%result3}}.[[aggregatable]]' => true,
                    ],
                    ['>=', '{{%battle3}}.[[created_at]]', $date->format(DateTimeInterface::ATOM)],
                    ['<', '{{%battle3}}.[[created_at]]',
                        $date->add(new DateInterval('P1D'))->format(DateTimeInterface::ATOM),
                    ],
                    ['<>', '{{%lobby3}}.[[key]]', 'private'],
                    ['<>', '{{%rule3}}.[[key]]', 'tricolor'],
                ])
                ->orderBy([
                    '{{%battle3}}.[[start_at]]' => SORT_ASC,
                ]);

            foreach ($query->each(500) as $battle) {
                if (count($battle->battlePlayer3s) === 8) {
                    if (!$header) {
                        fwrite(
                            $fh,
                            self::csvRow(
                                array_merge(
                                    [
                                        '# season',
                                        'period',
                                        'game-ver',
                                        'lobby',
                                        'mode',
                                        'stage',
                                        'time',
                                        'win',
                                        'knockout',
                                        'rank',
                                        'power',
                                        'alpha-inked',
                                        'alpha-ink-percent',
                                        'alpha-count',
                                        'alpha-color',
                                        'alpha-theme',
                                        'bravo-inked',
                                        'bravo-ink-percent',
                                        'bravo-count',
                                        'bravo-color',
                                        'bravo-theme',
                                    ],
                                    $playerColumns('A1'),
                                    $playerColumns('A2'),
                                    $playerColumns('A3'),
                                    $playerColumns('A4'),
                                    $playerColumns('B1'),
                                    $playerColumns('B2'),
                                    $playerColumns('B3'),
                                    $playerColumns('B4'),
                                    [
                                        'medal1-grade',
                                        'medal1-name',
                                        'medal2-grade',
                                        'medal2-name',
                                        'medal3-grade',
                                        'medal3-name',
                                        'event',
                                    ],
                                ),
                            ) . "\x0d\x0a",
                        );
                        $header = true;
                    }

                    fprintf(STDERR, "      #%d %s\n", $battle->id, $battle->start_at);

                    $csvColumns = [
                        self::season($battle),
                        gmdate(DateTimeInterface::ATOM, BattleHelper::periodToRange2($battle->period)[0]),
                        $battle->version->tag,
                        $battle->lobby->key,
                        $battle->rule->key,
                        $battle->map->key,
                        (string)(strtotime($battle->end_at) - strtotime($battle->start_at)),
                        $battle->result->is_win ? 'alpha' : 'bravo',
                        $battle->is_knockout === null ? '' : ($battle->is_knockout ? 'TRUE' : 'FALSE'),
                        self::rank($battle->rankBefore, $battle->rank_before_s_plus),
                        match ($battle->lobby?->key) {
                            'bankara_open' => self::powerFormat($battle->bankara_power_before),
                            'event' => self::powerFormat($battle->event_power),
                            'splatfest_challenge' => self::powerFormat($battle->fest_power),
                            'xmatch' => self::powerFormat($battle->x_power_before),
                            default => self::powerFormat(null),
                        },
                        (string)$battle->our_team_inked,
                        (string)$battle->our_team_percent,
                        (string)$battle->our_team_count,
                        (string)$battle->our_team_color,
                        (string)$battle->ourTeamTheme?->name,
                        (string)$battle->their_team_inked,
                        (string)$battle->their_team_percent,
                        (string)$battle->their_team_count,
                        (string)$battle->their_team_color,
                        (string)$battle->theirTeamTheme?->name,
                    ];

                    $players = ArrayHelper::sort(
                        $battle->battlePlayer3s,
                        function (BattlePlayer3 $a, BattlePlayer3 $b): int {
                            if ($a->is_our_team !== $b->is_our_team) {
                                return $a->is_our_team ? -1 : 1;
                            }

                            if ($a->is_me !== $b->is_me) {
                                return $a->is_me ? -1 : 1;
                            }

                            return $a->id <=> $b->id;
                        },
                    );
                    if (count($players) !== 8) {
                        $players = array_slice(
                            array_merge($players, [null, null, null, null, null, null, null, null]),
                            0,
                            8,
                        );
                    }
                    foreach ($players as $player) {
                        $csvColumns = array_merge($csvColumns, [
                            (string)$player?->weapon?->key,
                            (string)$player?->kill_or_assist,
                            (string)$player?->kill,
                            (string)$player?->assist,
                            (string)$player?->death,
                            (string)$player?->special,
                            (string)$player?->inked,
                            self::gearAbilities($player),
                        ]);
                    }

                    $medals = [];
                    foreach ($battle->medals as $medal) {
                        $medals = array_merge($medals, [
                            match ($medal?->canonical?->gold) {
                                true => 'gold',
                                false => 'silver',
                                default => '',
                            },
                            $medal?->canonical?->name ?? $medal?->name ?? '',
                        ]);
                    }
                    $csvColumns = array_merge(
                        $csvColumns,
                        array_slice(
                            array_merge($medals, ['', '', '', '', '', '']),
                            0,
                            6,
                        ),
                    );
                    $csvColumns[] = (string)$battle->event?->name;

                    fwrite($fh, self::csvRow($csvColumns) . "\x0d\x0a");
                }
            }
            if (!$header) {
                fwrite(STDERR, "    no data.\n");
                return true;
            }

            if (fseek($fh, 0, SEEK_SET) < 0) {
                fwrite(STDERR, "Seek failed\n");

                return false;
            }

            fwrite(STDERR, "    Copying\n");
            FileHelper::createDirectory(dirname($outPath));
            if (!$out = fopen($outPath, 'w')) {
                fwrite(STDERR, "Could not open outpath for writing\n");

                return false;
            }

            if (!stream_copy_to_stream($fh, $out, null)) {
                fclose($out);
                unlink($outPath);
                fwrite(STDERR, "Could not write CSV\n");

                return false;
            }

            fclose($out);
            fwrite(STDERR, "    done!\n");

            return true;
        } catch (Throwable $e) {
            vfprintf(STDERR, "Catch exception, exception=%s, message=%s\n", [
                $e::class,
                $e->getMessage(),
            ]);

            return false;
        } finally {
            fclose($fh);
        }
    }

    private function createBattleResultsCsvZip(): bool
    {
        return self::createCsvZipArchive(self::BASE_BATTLE_RESULTS_CSV);
    }
}
