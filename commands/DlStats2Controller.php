<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use ZipArchive;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle2;
use app\models\BattlePlayer2;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class DlStats2Controller extends Controller
{
    public const BASE_BATTLE_RESULTS_CSV = '@app/runtime/dl-stats/splatoon-2/battle-results-csv';

    public $defaultAction = 'create';

    public function init()
    {
        parent::init();
        Yii::$app->timeZone = 'Asia/Tokyo';
    }

    public function actionCreate()
    {
        $this->actionCreateBattleResultsCsv();
    }

    public function actionCreateBattleResultsCsv()
    {
        $aDay = new DateInterval('P1D');
        for ($date = static::startDay(), $end = static::today(); $date < $end; $date = $date->add($aDay)) {
            printf("%s - %s\n", __METHOD__, $date->format('Y-m-d'));
            $file = implode('/', [
                Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV),
                $date->format('Y'),
                $date->format('m'),
                $date->format('Y-m-d') . '.csv',
            ]);
            if (file_exists($file)) {
                echo "  => exists\n";
            } else {
                echo "  => creating CSV\n";
                $this->createBattleResultsCsv($date, $file);
            }
        }

        $this->createBattleResultsCsvZip();
    }

    private function createBattleResultsCsv(DateTimeImmutable $date, string $outPath): bool
    {
        // {{{
        $header = false;
        if (!$fh = tmpfile()) {
            echo "tmpfile() failed\n";
            return false;
        }
        try {
            $playerColumns = fn (string $prefix): array => [
                    $prefix . '-weapon',
                    $prefix . '-kill-assist',
                    $prefix . '-kill',
                    $prefix . '-assist',
                    $prefix . '-death',
                    $prefix . '-special',
                    $prefix . '-inked',
                    $prefix . '-rank',
                    $prefix . '-level',
                ];
            $playerCsv = function (Battle2 $b, ?BattlePlayer2 $p): array {
                if (!$p) {
                    return ['', '', '', '', '', '', '', '', ''];
                }

                $inked = (function (?int $point) use ($b, $p): ?int {
                    if ($point === null) {
                        return null;
                    }

                    if ($b->is_win == $p->is_my_team) {
                        if ($b->rule->key === 'nawabari') {
                            return $point - 1000;
                        }
                    }

                    return $point;
                });

                return [
                    $p->weapon ? $p->weapon->key : '',
                    (string)$p->kill_or_assist,
                    (string)$p->kill,
                    ($p->kill_or_assist !== null && $p->kill !== null)
                        ? (string)($p->kill_or_assist - $p->kill)
                        : '',
                    (string)$p->death,
                    (string)$p->special,
                    (string)$inked($p->point),
                    $p->rank ? $p->rank->key : '',
                    (string)(((int)$p->star_rank * 99) + $p->level),
                ];
            };

            $query = Battle2::find()
                ->innerJoinWith(['lobby', 'mode', 'rule', 'map'])
                ->with([
                    'myTeamPlayers',
                    'hisTeamPlayers',
                    'agent',
                ])
                ->andWhere(['and',
                    ['>=', 'battle2.created_at', $date->format(DateTime::ATOM)],
                    ['<', 'battle2.created_at', $date->add(new DateInterval('P1D'))->format(DateTime::ATOM)],
                    ['not', ['battle2.is_win' => null]],
                    ['not', ['battle2.start_at' => null]],
                    ['not', ['battle2.end_at' => null]],
                    'battle2.start_at < battle2.end_at',
                    ['<>', 'lobby2.key', 'private'],
                    ['<>', 'mode2.key', 'private'],
                    [
                        'battle2.is_automated' => true,
                        'battle2.use_for_entire' => true,
                    ],
                ])
                ->orderBy(['battle2.start_at' => SORT_ASC]);
            foreach ($query->each(500) as $battle) {
                if (count($battle->myTeamPlayers) && count($battle->hisTeamPlayers)) {
                    if (!$header) {
                        fwrite($fh, static::csvRow(array_merge(
                            [
                                '# period',
                                'game-ver',
                                'lobby-mode',
                                'lobby',
                                'mode',
                                'stage',
                                'time',
                                'win',
                                'knockout',
                            ],
                            $playerColumns('A1'),
                            $playerColumns('A2'),
                            $playerColumns('A3'),
                            $playerColumns('A4'),
                            $playerColumns('B1'),
                            $playerColumns('B2'),
                            $playerColumns('B3'),
                            $playerColumns('B4'),
                        )) . "\x0d\x0a");
                        $header = true;
                    }
                    printf("      #%d %s\n", $battle->id, $battle->start_at);

                    $csv = static::csvRow([
                        gmdate(DateTime::ATOM, BattleHelper::periodToRange2($battle->period)[0]),
                        $battle->version->tag ?? '',
                        $battle->mode->key,
                        $battle->lobby->key,
                        $battle->rule->key,
                        $battle->map->key,
                        (string)(strtotime($battle->end_at) - strtotime($battle->start_at)),
                        $battle->is_win ? 'alpha' : 'bravo',
                        $battle->is_knockout === null ? '' : ($battle->is_knockout ? 'TRUE' : 'FALSE'),
                    ]);

                    $team = $battle->myTeamPlayers;
                    usort($team, function ($a, $b) {
                        if ($a->is_me) {
                            return -1;
                        } elseif ($b->is_me) {
                            return 1;
                        } else {
                            return 0;
                        }
                    });
                    for ($i = 0; $i < 4; ++$i) {
                        $csv .= ',' . static::csvRow($playerCsv($battle, $team[$i] ?? null));
                    }
                    $team = $battle->hisTeamPlayers;
                    for ($i = 0; $i < 4; ++$i) {
                        $csv .= ',' . static::csvRow($playerCsv($battle, $team[$i] ?? null));
                    }
                    fwrite($fh, $csv . "\x0d\x0a");
                }
            }
            fseek($fh, 0, SEEK_SET);

            echo "    Copying\n";
            FileHelper::createDirectory(dirname($outPath));
            if (!$out = fopen($outPath, 'w')) {
                echo "Could not open outpath for writing\n";
                return false;
            }
            if (!stream_copy_to_stream($fh, $out)) {
                fclose($out);
                unlink($outPath);
                echo "Could not write CSV\n";
                return false;
            }
            fclose($out);
            echo "    done!\n";

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            return false;
        } finally {
            fclose($fh);
        }
        // }}}
    }

    private function createBattleResultsCsvZip(): bool
    {
        // {{{
        if (!$tmpFile = tempnam('/tmp', 'zip-')) {
            return false;
        }
        try {
            $zip = new ZipArchive();
            if (!$zip->open($tmpFile, ZipArchive::CREATE)) {
                return false;
            }
            if (!$zip->addEmptyDir(basename(Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV)))) {
                return false;
            }
            if (
                !$zip->addGlob(
                    Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV) . '/*/*/*.csv',
                    0,
                    [
                    'add_path' => basename(Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV)) . '/',
                    'remove_all_path' => true,
                    ],
                )
            ) {
                return false;
            }
            if (!$zip->close()) {
                return false;
            }
            copy(
                $tmpFile,
                implode('/', [
                    Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV),
                    basename(Yii::getAlias(static::BASE_BATTLE_RESULTS_CSV)) . '.zip',
                ]),
            );
            return true;
        } finally {
            unlink($tmpFile);
        }
        // }}}
    }

    private static function startDay(): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Asia/Tokyo'))
            ->setDate(2017, 8, 10)
            ->setTime(0, 0, 0);
    }

    private static function today(): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Asia/Tokyo'))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTime(0, 0, 0);
    }

    private static function csvRow(array $cols): string
    {
        return implode(',', array_map(
            function (string $col): string {
                if (
                    strpos($col, ',') === false &&
                    strpos($col, "\n") === false &&
                    strpos($col, '"') === false
                ) {
                    return $col;
                } else {
                    return '"' . str_replace('"', '""', $col) . '"';
                }
            },
            $cols,
        ));
    }
}
