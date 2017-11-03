<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use app\models\Map2;
use app\models\WeaponCategory2;
use yii\console\Controller;
use yii\helpers\Console;

class Api2MarkdownController extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate() : int
    {
        $path = Yii::getAlias('@app/doc/api-2/post-battle.md');
        $markdown = preg_replace_callback(
            '/(?<start><!--replace:(?<kind>[\w-]+)-->)(?<oldvalue>.*?)(?<end><!--endreplace-->)/us',
            function (array $match) : string {
                switch ($match['kind']) {
                    case 'weapon':
                        ob_start();
                        $status = $this->actionWeapon();
                        if ($status !== 0) {
                            return $status;
                        }
                        $repl = ob_get_clean();
                        return $match['start'] . "\n" . rtrim($repl) . "\n" . $match['end'];

                    case 'stage':
                        ob_start();
                        $status = $this->actionStage();
                        if ($status !== 0) {
                            return $status;
                        }
                        $repl = ob_get_clean();
                        return $match['start'] . "\n" . rtrim($repl) . "\n" . $match['end'];

                    default:
                        $this->stderr("Unknown kind of replace tag: " . $match['kind'] . "\n");
                        exit(1);
                }
            },
            file_get_contents($path)
        );
        file_put_contents($path, $markdown);
        echo "Updated $path\n";
        return 0;
    }

    public function actionWeapon() : int
    {
        // {{{
        $compats = [
            'maneuver_collabo' => 'manueuver_collabo',
            'maneuver' => 'manueuver',
        ];
        $data = [
            [
                "指定文字列<br>Key String",
                "イカリング<br>SplatNet",
                "ブキ<br>Weapon Name",
                "備考<br>Remarks",
            ],
        ];
        $categories = WeaponCategory2::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        foreach ($categories as $category) {
            $types = $category->getWeaponTypes()
                ->orderBy([
                    'category_id' => SORT_ASC,
                    'rank' => SORT_ASC,
                ])
                ->all();
            foreach ($types as $type) {
                $weapons = $type->getWeapons()
                    ->orderBy(['key' => SORT_ASC])
                    ->asArray()
                    ->all();
                foreach ($weapons as $weapon) {
                    $remarks = [];
                    if (isset($compats[$weapon['key']])) {
                        $remarks[] = sprintf(
                            '互換性のため %s も受け付けます',
                            implode(', ', array_map(
                                function (string $value) : string {
                                    return '`' . $value . '`';
                                },
                                (array)$compats[$weapon['key']]
                            ))
                        );
                        $remarks[] = sprintf(
                            'Also accepts %s for compatibility',
                            implode(', ', array_map(
                                function (string $value) : string {
                                    return '`' . $value . '`';
                                },
                                (array)$compats[$weapon['key']]
                            ))
                        );
                    }
                    $data[] = [
                        sprintf('`%s`', $weapon['key']),
                        isset($weapon['splatnet']) ? sprintf('`%d`', $weapon['splatnet']) : '',
                        implode('<br>', [
                            Yii::t('app-weapon2', $weapon['name'], [], 'ja-JP'),
                            $weapon['name'],
                        ]),
                        implode('<br>', $remarks),
                    ];
                }
            }
        }
        echo static::createTable($data);
        return 0;
        // }}}
    }

    public function actionStage() : int
    {
        // {{{
        $compats = [
            'kombu' => 'combu',
        ];
        $remarks = [
            'mystery' => [
                'フェス専用ステージ',
                'For Splatfest',
            ],
        ];
        $maps = Map2::find()->all();
        usort($maps, function (Map2 $a, Map2 $b) : int {
            if ($a->key === $b->key) {
                return 0;
            } elseif ($a->key === 'mystery') {
                return 1;
            } elseif ($b->key === 'mystery') {
                return -1;
            } else {
                return strcmp($a->key, $b->key);
            }
        });

        $data = [
            [
                "指定文字列<br>Key String",
                "イカリング<br>SplatNet",
                "ステージ<br>Stage Name",
                "備考<br>Remarks",
            ],
        ];
        foreach ($maps as $map) {
            // {{{
            $colRemarks = $remarks[$map->key] ?? [];
            if (isset($compats[$map->key])) {
                $colRemarks[] = sprintf(
                    '互換性のため %s も受け付けます',
                    implode(', ', array_map(
                        function (string $value) : string {
                            return '`' . $value . '`';
                        },
                        (array)$compats[$map->key]
                    ))
                );
                $colRemarks[] = sprintf(
                    'Also accepts %s for compatibility',
                    implode(', ', array_map(
                        function (string $value) : string {
                            return '`' . $value . '`';
                        },
                        (array)$compats[$map->key]
                    ))
                );
            }
            $data[] = [
                sprintf('`%s`', $map->key),
                $map->splatnet !== null ? sprintf('`%d`', $map->splatnet) : '',
                implode("<br>", [
                    Yii::t('app-map2', $map->name, [], 'ja-JP'),
                    $map->name,
                ]),
                implode("<br>", $colRemarks),
            ];
            // }}}
        }
        echo static::createTable($data);
        return 0;
        // }}}
    }

    // Markdown Table {{{
    private static function calcWidths(array $rows) : array
    {
        $widths = [];
        foreach ($rows as $row) {
            foreach (array_values($row) as $i => $column) {
                $w = static::calcStringWidth($column);
                $widths[$i] = max($widths[$i] ?? 0, $w);
            }
        }
        return $widths;
    }

    private static function calcStringWidth(string $text, int $minWidth = 1) : int
    {
        // {{{
        $lines = preg_split('/\x0d\x0a|\x0d|\x0a/s', $text);
        return array_reduce(
            array_map(
                function (string $line) : int {
                    return self::strwidth($line);
                },
                $lines
            ),
            function (int $a, int $b) : int {
                return max($a, $b);
            },
            $minWidth
        );
        // }}}
    }

    private static function createTable(array $rows) : string
    {
        // {{{
        $widths = static::calcWidths($rows);

        $result = '';
        foreach (array_values($rows) as $i => $row) {
            $result .= static::createTableRow($row, $widths) . "\n";
            if ($i === 0) {
                $result .= sprintf("|%s|\n", implode('|', array_map(
                    function (int $cellWidth) : string {
                        return str_repeat('-', $cellWidth);
                    },
                    $widths
                )));
            }
        }
        return $result;
        // }}}
    }

    private static function createTableRow(array $row, array $widths) : string
    {
        $outColumns = [];
        foreach (array_values($row) as $i => $colText) {
            $outColumns[] = $colText . str_repeat(' ', $widths[$i] - self::strwidth($colText));
        }
        return '|' . implode('|', $outColumns) . '|';
    }

    private static function strwidth(string $text) : int
    {
        // mb_strwidth may returns wrong value for East Asian Ambiguous Width
        $width = mb_strwidth($text, 'UTF-8');
        $additional = 0;
        $additional += preg_match_all('/[Α-Ωα-ω]/u', $text, $matches);
        return $width + $additional;
    }
    // }}}
}
