<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
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
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\SalmonPlayerWeapon3;
use app\models\SalmonWave3;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;

use function array_fill;
use function array_map;
use function array_merge;
use function dirname;
use function fclose;
use function file_exists;
use function fopen;
use function fprintf;
use function fseek;
use function fwrite;
use function gmdate;
use function implode;
use function ksort;
use function stream_copy_to_stream;
use function strtotime;
use function tmpfile;
use function unlink;
use function vfprintf;
use function vsprintf;

use const SEEK_SET;
use const SORT_ASC;
use const SORT_STRING;
use const STDERR;

trait SalmonTrait
{
    use CsvUtilTrait;
    use DateUtilTrait;
    use ModelUtilTrait;
    use ZipUtilTrait;

    public function actionCreateSalmonResultsCsv(): int
    {
        $allSuccess = true;

        $aDay = new DateInterval('P1D');
        $sDay = self::startDay();
        $eDay = self::today();
        for ($date = $sDay; $date < $eDay; $date = $date->add($aDay)) {
            fprintf(STDERR, "%s - %s\n", __METHOD__, $date->format('Y-m-d'));

            $file = implode('/', [
                Yii::getAlias(self::BASE_SALMON_RESULTS_CSV),
                $date->format('Y'),
                $date->format('m'),
                $date->format('Y-m-d') . '.csv',
            ]);

            if (file_exists($file)) {
                fwrite(STDERR, "  => exists\n");
                continue;
            }

            fwrite(STDERR, "  => creating CSV\n");
            if (!$this->createSalmonResultsCsv($date, $file)) {
                $allSuccess = false;
            }
        }

        if (!$this->createSalmonResultsCsvZip()) {
            $allSuccess = false;
        }

        return $allSuccess ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    private function createSalmonResultsCsv(DateTimeImmutable $date, string $outPath): bool
    {
        $header = false;
        if (!$fh = @tmpfile()) {
            fwrite(STDERR, "tmpfile() failed\n");

            return false;
        }

        try {
            $waveColumns = fn (?int $waveNumber): array => array_map(
                fn (string $column) => vsprintf('%s-%s', [
                    $waveNumber === null ? 'xtrawave' : "wave-{$waveNumber}",
                    $column,
                ]),
                [
                    'hazard-level',
                    'water-level',
                    'event',
                    'golden-quota',
                    'golden-delivered',
                    'golden-appearances',
                    'special-uses',
                ],
            );

            $playerColumns = fn (int $number): array => array_map(
                fn (string $column) => "player-{$number}-{$column}",
                [
                    'special',
                    'golden-egg',
                    'assists',
                    'power-egg',
                    'rescues',
                    'rescued',
                    'defeat-boss',
                    'weapon-wave-1',
                    'weapon-wave-2',
                    'weapon-wave-3',
                    'weapon-wave-4',
                    'weapon-wave-5',
                    'weapon-xtrawave',
                ],
            );

            $notNull = fn (string $identifier): array => ['not', [$identifier => null]];
            $query = Salmon3::find()
                ->innerJoinWith([
                    'schedule',
                    'version',
                ])
                ->with([
                    'bigStage',
                    'kingSalmonid',
                    'salmonBossAppearance3s',
                    'salmonBossAppearance3s.boss',
                    'salmonPlayer3s',
                    'salmonPlayer3s.salmonPlayerWeapon3s',
                    'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                    'salmonPlayer3s.special',
                    'salmonWave3s',
                    'salmonWave3s.event',
                    'salmonWave3s.salmonSpecialUse3s',
                    'salmonWave3s.salmonSpecialUse3s.special',
                    'salmonWave3s.tide',
                    'stage',
                    'titleAfter',
                    'titleBefore',
                ])
                ->andWhere(['and',
                    $notNull('{{%salmon3}}.[[start_at]]'),
                    $notNull('{{%salmon3}}.[[clear_waves]]'),
                    $notNull('{{%salmon3}}.[[version_id]]'),
                    [
                        '{{%salmon3}}.[[has_broken_data]]' => false,
                        '{{%salmon3}}.[[has_disconnect]]' => false,
                        '{{%salmon3}}.[[is_automated]]' => true,
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                    ],
                    ['>=', '{{%salmon3}}.[[created_at]]', $date->format(DateTimeInterface::ATOM)],
                    ['<', '{{%salmon3}}.[[created_at]]',
                        $date->add(new DateInterval('P1D'))->format(DateTimeInterface::ATOM),
                    ],
                    ['or',
                        '{{%salmon3}}.[[is_big_run]] = TRUE AND {{%salmon3}}.[[big_stage_id]] IS NOT NULL',
                        '{{%salmon3}}.[[is_big_run]] = FALSE AND {{%salmon3}}.[[stage_id]] IS NOT NULL',
                    ],
                ])
                ->orderBy([
                    '{{%salmon3}}.[[start_at]]' => SORT_ASC,
                    '{{%salmon3}}.[[id]]' => SORT_ASC,
                ]);

            foreach ($query->each(500) as $battle) {
                if (!$header) {
                    fwrite(
                        $fh,
                        self::csvRow(
                            array_merge(
                                [
                                    '# season',
                                    'rotation',
                                    'game-ver',
                                    'lobby',
                                    'stage',
                                    'cleared',
                                    'cleared-waves',
                                    'salmometer',
                                    'king-salmonid',
                                    'cleared-extra',
                                    'hazard-level',
                                    'title-before',
                                    'title-rank-before',
                                    'title-after',
                                    'title-rank-after',
                                    'golden-egg',
                                    'power-egg',
                                    'gold-scale',
                                    'silver-scale',
                                    'bronze-scale',
                                    'job-point',
                                    'job-score',
                                    'job-rate',
                                    'job-bonus',
                                ],
                                $waveColumns(1),
                                $waveColumns(2),
                                $waveColumns(3),
                                $waveColumns(4),
                                $waveColumns(5),
                                $waveColumns(null),
                                $playerColumns(1),
                                $playerColumns(2),
                                $playerColumns(3),
                                $playerColumns(4),
                                ['boss'],
                            ),
                        ) . "\x0d\x0a",
                    );
                    $header = true;
                }

                fprintf(STDERR, "      #%d %s\n", $battle->id, $battle->start_at);

                $csvColumns = [
                    self::season($battle),
                    $battle->schedule
                        ? gmdate(DateTimeInterface::ATOM, strtotime($battle->schedule->start_at))
                        : '',
                    $battle->version->tag,
                    match (true) {
                        $battle->is_big_run => 'big_run',
                        $battle->is_eggstra_work => 'eggstra_work',
                        default => 'normal',
                    },
                    match (true) {
                        $battle->is_big_run => $battle->bigStage?->key,
                        default => $battle->stage?->key,
                    },
                    match (true) {
                        $battle->is_eggstra_work => ($battle->clear_waves >= 5 ? 'TRUE' : 'FALSE'),
                        default => ($battle->clear_waves >= 3 ? 'TRUE' : 'FALSE'),
                    },
                    $battle->clear_waves,
                    $battle->king_smell,
                    $battle->kingSalmonid?->key,
                    $battle->kingSalmonid && $battle->clear_extra !== null
                        ? ($battle->clear_extra ? 'TRUE' : 'FALSE')
                        : '',
                    match (true) {
                        $battle->is_eggstra_work => '',
                        $battle->danger_rate === null => '',
                        default => (string)(int)$battle->danger_rate,
                    },
                    $battle->titleBefore?->key,
                    $battle->titleBefore && $battle->title_exp_before !== null
                        ? $battle->title_exp_before
                        : '',
                    $battle->titleAfter?->key,
                    $battle->titleAfter && $battle->title_exp_after !== null
                        ? $battle->title_exp_after
                        : '',
                    $battle->golden_eggs,
                    $battle->power_eggs,
                    $battle->kingSalmonid && $battle->gold_scale !== null ? $battle->gold_scale : '',
                    $battle->kingSalmonid && $battle->silver_scale !== null ? $battle->silver_scale : '',
                    $battle->kingSalmonid && $battle->bronze_scale !== null ? $battle->bronze_scale : '',
                    $battle->job_point,
                    $battle->job_score,
                    $battle->job_rate,
                    $battle->job_bonus,
                ];

                // waves
                $csvColumns = array_merge(
                    $csvColumns,
                    self::salmonWaveCsvColumns(self::findSalmonWave($battle->salmonWave3s, 1)),
                    self::salmonWaveCsvColumns(self::findSalmonWave($battle->salmonWave3s, 2)),
                    self::salmonWaveCsvColumns(self::findSalmonWave($battle->salmonWave3s, 3)),
                    self::salmonWaveCsvColumns(
                        $battle->is_eggstra_work ? self::findSalmonWave($battle->salmonWave3s, 4) : null,
                    ),
                    self::salmonWaveCsvColumns(
                        $battle->is_eggstra_work ? self::findSalmonWave($battle->salmonWave3s, 5) : null,
                    ),
                    self::salmonWaveCsvColumns(
                        $battle->is_eggstra_work ? null : self::findSalmonWave($battle->salmonWave3s, 4),
                    ),
                );

                // players
                $players = ArrayHelper::sort(
                    $battle->salmonPlayer3s,
                    function (SalmonPlayer3 $a, SalmonPlayer3 $b): int {
                        if ($a->is_me !== $b->is_me) {
                            return $a->is_me ? -1 : 1;
                        }

                        return $a->id <=> $b->id;
                    },
                );
                $csvColumns = array_merge(
                    $csvColumns,
                    self::salmonPlayerCsvColumns($battle, $players[0] ?? null),
                    self::salmonPlayerCsvColumns($battle, $players[1] ?? null),
                    self::salmonPlayerCsvColumns($battle, $players[2] ?? null),
                    self::salmonPlayerCsvColumns($battle, $players[3] ?? null),
                );

                // bosses
                $bosses = [];
                foreach ($battle->salmonBossAppearance3s as $tmp) {
                    $k = $tmp->boss?->key;
                    if ($k) {
                        $bosses[$k] = [
                            'appearances' => $tmp->appearances,
                            'defeated' => $tmp->defeated,
                        ];
                    }
                }
                if ($bosses) {
                    ksort($bosses, SORT_STRING);
                    $csvColumns[] = Json::encode($bosses);
                } else {
                    $csvColumns[] = '';
                }

                fwrite($fh, self::csvRow($csvColumns) . "\x0d\x0a");
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

    private function createSalmonResultsCsvZip(): bool
    {
        return self::createCsvZipArchive(self::BASE_SALMON_RESULTS_CSV);
    }

    private static function findSalmonWave(array $waves, int $waveNumber): ?SalmonWave3
    {
        foreach ($waves as $wave) {
            if ($wave->wave === $waveNumber) {
                return $wave;
            }
        }

        return null;
    }

    private static function salmonWaveCsvColumns(?SalmonWave3 $wave): array
    {
        if (!$wave) {
            return array_fill(0, 7, '');
        }

        $specials = '';
        if ($wave->salmonSpecialUse3s) {
            $data = [];
            foreach ($wave->salmonSpecialUse3s as $tmp) {
                $k = $tmp->special?->key;
                if ($k) {
                    $data[$k] = (int)$tmp->count;
                }
            }
            ksort($data, SORT_STRING);
            $specials = Json::encode($data);
        }

        return [
            $wave->danger_rate === null ? '' : (string)(int)$wave->danger_rate,
            $wave->tide?->key,
            $wave->event?->key,
            $wave->golden_quota,
            $wave->golden_delivered,
            $wave->golden_appearances,
            $specials,
        ];
    }

    private static function salmonPlayerCsvColumns(Salmon3 $battle, ?SalmonPlayer3 $player): array
    {
        if (!$player) {
            return array_fill(0, 13, '');
        }

        $weapons = ArrayHelper::sort(
            $player->salmonPlayerWeapon3s,
            fn (SalmonPlayerWeapon3 $a, SalmonPlayerWeapon3 $b): int => $a->wave <=> $b->wave,
        );

        $isEggstra = $battle->is_eggstra_work;
        return [
            $player->special?->key,
            $player->golden_eggs,
            $player->golden_assist,
            $player->power_eggs,
            $player->rescue,
            $player->rescued,
            $player->defeat_boss,
            ($weapons[0] ?? null)?->weapon?->key,
            ($weapons[1] ?? null)?->weapon?->key,
            ($weapons[2] ?? null)?->weapon?->key,
            $isEggstra ? ($weapons[3] ?? null)?->weapon?->key : '',
            $isEggstra ? ($weapons[4] ?? null)?->weapon?->key : '',
            $isEggstra ? '' : ($weapons[3] ?? null)?->weapon?->key,
        ];
    }
}
