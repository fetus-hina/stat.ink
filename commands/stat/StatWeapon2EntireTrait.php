<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Lobby2;
use app\models\Mode2;
use app\models\Rank2;
use app\models\Rule2;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;

trait StatWeapon2EntireTrait
{
    // private const TYPE_MAIN = '_main';
    // private const TYPE_SUB = '_sub';
    // private const TYPE_SPECIAL = '_special';

    // private const VERSION_ANY = '';
    // private const VERSION_VERSION = '_by_version';
    // private const VERSION_VGROUP = '_by_vgroup';

    protected function updateStatWeapon2Entire(): void
    {
        $types = ['_main', '_sub', '_special'];
        $versions = [null, '_by_version', '_by_vgroup'];

        Yii::$app->db->transaction(
            function (Connection $db) use ($types, $versions): void {
                $now = new DateTimeImmutable('now', new DateTimeZone('Etc/UTC'));

                foreach ($types as $type) {
                    foreach ($versions as $version) {
                        $this->makeStatWeapon2Entire($db, $now, $type, $version);
                    }
                }
            },
            Transaction::REPEATABLE_READ
        );
    }

    private function makeStatWeapon2Entire(
        Connection $db,
        DateTimeImmutable $now,
        string $typeSuffix,
        ?string $versionSuffix
    ): void {
        $targetTableName = 'stat_weapon2_entire' . $typeSuffix . $versionSuffix;
        fwrite(STDERR, "Updating {$targetTableName}...\n");

        $ruleNawabari = Rule2::findOne(['key' => 'nawabari']);
        $modeGachi = Mode2::findOne(['key' => 'gachi']);
        $modeFest = Mode2::findOne(['key' => 'fest']);
        $profreshionalRankIds = array_map(
            fn ($rank) => (int)$rank->id,
            Rank2::find()->andWhere(['key' => ['s+', 'x']])->all(),
        );

        $select = (new Query()) // {{{
            ->select(array_merge(
                ['rule_id' => '{{b2}}.[[rule_id]]'],
                $this->getVersionColumns($versionSuffix),
                $this->getTypeColumns($typeSuffix),
                [
                    'battles' => 'COUNT(*)',
                    'wins' => 'SUM(CASE WHEN {{b2}}.[[is_win]] = {{p}}.[[is_my_team]] THEN 1 ELSE 0 END)',
                ],
                $this->getMetricColumns('kill'),
                $this->getMetricColumns('death'),
                $this->getMetricColumns('special'),
                $this->getMetricColumns('point', sprintf('({{p}}.[[point]] - (CASE %s END))', implode(' ', [
                    'WHEN {{b2}}.[[rule_id]] <> ' . $ruleNawabari->id . ' THEN 0',
                    'WHEN {{b2}}.[[is_win]] = {{p}}.[[is_my_team]] THEN 1000',
                    'ELSE 0',
                ]))),
                [
                    'avg_time' => sprintf('AVG(CASE %s END)', implode(' ', [
                        'WHEN {{b2}}.[[rule_id]] = ' .  $ruleNawabari->id . ' THEN 180',
                        'ELSE EXTRACT(EPOCH FROM {{b2}}.[[end_at]] - {{b2}}.[[start_at]])',
                    ])),
                    'updated_at' => new Expression($db->quoteValue($now->format(DateTime::ATOM))),
                ],
            ))
            ->from(['b2' => 'battle2'])
            ->innerJoin(['v' => 'splatoon_version2'], '{{b2}}.[[version_id]] = {{v}}.[[id]]')
            ->innerJoin(['p' => 'battle_player2'], '{{b2}}.[[id]] = {{p}}.[[battle_id]]')
            ->innerJoin(['w' => 'weapon2'], '{{p}}.[[weapon_id]] = {{w}}.[[id]]')
            ->groupBy([
                '{{b2}}.[[rule_id]]',
                ...array_values($this->getVersionColumns($versionSuffix)),
                ...array_values($this->getTypeColumns($typeSuffix)),
            ])
            ->andWhere(['and',
                ['not', ['{{b2}}.[[lobby_id]]' => null]],
                ['not', ['{{b2}}.[[mode_id]]' => null]],
                ['not', ['{{b2}}.[[rule_id]]' => null]],
                ['not', ['{{b2}}.[[map_id]]' => null]],
                ['not', ['{{b2}}.[[weapon_id]]' => null]],
                ['not', ['{{b2}}.[[is_win]]' => null]],
                ['not', ['{{b2}}.[[version_id]]' => null]],
                ['not', ['{{b2}}.[[start_at]]' => null]],
                ['not', ['{{b2}}.[[end_at]]' => null]],
                ['not', ['{{p}}.[[weapon_id]]' => null]],
                ['not', ['{{p}}.[[kill]]' => null]],
                ['not', ['{{p}}.[[death]]' => null]],
                ['not', ['{{p}}.[[special]]' => null]],
                ['not', ['{{p}}.[[point]]' => null]],
                ['<>', '{{b2}}.[[lobby_id]]', Lobby2::findOne(['key' => 'private'])->id],
                ['<>', '{{b2}}.[[mode_id]]', Mode2::findOne(['key' => 'private'])->id],
                ['{{b2}}.[[is_automated]]' => true],
                ['{{b2}}.[[use_for_entire]]' => true],
                ['{{b2}}.[[has_disconnect]]' => false],
                ['{{p}}.[[is_me]]' => false],
                ['<', '{{b2}}.[[period]]', BattleHelper::calcPeriod2($now->setTime(0, 0, 0)->getTimestamp())],
                ['or',
                    ['and',
                        ['{{b2}}.[[rule_id]]' => $ruleNawabari->id],
                    ],
                    ['and',
                        ['not', ['{{b2}}.[[rule_id]]' => $ruleNawabari->id]],
                        [
                            '>=',
                            '({{b2}}.[[end_at]] - {{b2}}.[[start_at]])',
                            new Expression('(:minTime)::interval', [':minTime' => '30 seconds']),
                        ],
                    ],
                ],
                ['or',
                    // レギュラーマッチは（自分以外）全員分使う
                    ['and',
                        ['{{b2}}.[[rule_id]]' => $ruleNawabari->id],
                        ['<>', '{{b2}}.[[mode_id]]', $modeFest->id],
                    ],
                    // フェスマッチは敵チームのデータを使う
                    ['and',
                        ['{{b2}}.[[rule_id]]' => $ruleNawabari->id],
                        ['{{b2}}.[[mode_id]]' => $modeFest->id],
                        ['{{p}}.[[is_my_team]]' => false],
                    ],
                    // ガチマッチは自分以外の全員分使う。ただし、S+ と X のみ
                    ['and',
                        ['{{b2}}.[[lobby_id]]' => Lobby2::findOne(['key' => 'standard'])->id],
                        ['{{b2}}.[[mode_id]]' => $modeGachi->id],
                        ['{{p}}.[[rank_id]]' => $profreshionalRankIds],
                        ['or',
                            ['{{b2}}.[[rank_id]]' => $profreshionalRankIds],
                            ['{{b2}}.[[rank_after_id]]' => $profreshionalRankIds],
                        ],
                    ],
                    // リーグマッチは敵チームのデータを使う。ただし、S+ と X のみ
                    ['and',
                        ['{{b2}}.[[lobby_id]]' => array_map(
                            fn ($_) => (int)$_->id,
                            Lobby2::findAll(['key' => ['squad_2', 'squad_4']]),
                        )],
                        ['{{b2}}.[[mode_id]]' => $modeGachi->id],
                        ['{{p}}.[[rank_id]]' => $profreshionalRankIds],
                        ['{{p}}.[[is_my_team]]' => false],
                        ['or',
                            ['{{b2}}.[[rank_id]]' => $profreshionalRankIds],
                            ['{{b2}}.[[rank_after_id]]' => $profreshionalRankIds],
                        ],
                    ],
                ],
            ]);
        // }}}
        $sql = vsprintf('INSERT INTO %1$s (%2$s) %3$s ON CONFLICT ON CONSTRAINT %5$s DO UPDATE SET %4$s', [
            $db->quoteTableName($targetTableName),
            implode(', ', array_map(fn ($_) => $db->quoteColumnName($_), array_keys($select->select))),
            $select->createCommand()->rawSql,
            implode(', ', array_map(
                fn ($_) => vsprintf('%1$s = %2$s.%1$s', [
                    $db->quoteColumnName($_),
                    $db->quoteTableName('excluded'),
                ]),
                array_filter(
                    array_keys($select->select),
                    fn ($_) => !in_array(
                        $_,
                        ['rule_id', 'special_id', 'subweapon_id', 'version_group_id', 'version_id', 'weapon_id'],
                        true
                    ),
                ),
            )),
            $db->quoteColumnName($targetTableName . '_pkey'),
        ]);

        $t1 = microtime(true);
        $db->createCommand($sql)->execute();
        $t2 = microtime(true);
        printf("done, %.3f sec\n", $t2 - $t1);
        echo "cleanup...\n";
        $db->createCommand()
            ->delete(
                $targetTableName,
                ['<>', 'updated_at', $now->format(DateTime::ATOM)]
            )
            ->execute();
        $t3 = microtime(true);
        printf("done, %.3f sec\n", $t3 - $t2);
    }

    private function getVersionColumns(?string $versionTag): array
    {
        if ($versionTag === null) {
            return [];
        } elseif ($versionTag === '_by_version') {
            return ['version_id' => '{{b2}}.[[version_id]]'];
        } elseif ($versionTag === '_by_vgroup') {
            return ['version_group_id' => '{{v}}.[[group_id]]'];
        }

        throw new InvalidArgumentException();
    }

    private function getTypeColumns(string $typeTag): array
    {
        switch ($typeTag) {
            case '_main':
                return ['weapon_id' => '{{w}}.[[id]]'];

            case '_sub':
                return ['subweapon_id' => '{{w}}.[[subweapon_id]]'];

            case '_special':
                return ['special_id' => '{{w}}.[[special_id]]'];

            default:
                throw new InvalidArgumentException();
        }
    }

    private function getMetricColumns(string $metric, ?string $value = null): array
    {
        if ($value === null) {
            $value = "{{p}}.[[{$metric}]]";
        }

        return [
            "avg_{$metric}" => "AVG({$value})",
            "stddev_{$metric}" => "STDDEV_SAMP({$value})",
            "med_{$metric}" => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$value})",
        ];
    }
}
