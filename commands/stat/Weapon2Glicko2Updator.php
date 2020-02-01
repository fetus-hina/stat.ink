<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use DateTime;
use Diegobanos\Glicko2\Glicko2;
use Diegobanos\Glicko2\Rating\Rating;
use Diegobanos\Glicko2\Result\Result;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle2;
use app\models\BattlePlayer2;
use app\models\Rank2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\Weapon2;
use app\models\Weapon2RatingGlicko2;
use yii\base\Component;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Weapon2Glicko2Updator extends Component
{
    private const DEFAULT_RATING = 1500.0;
    private const DEFAULT_RD = 250.0;
    private const VOLATILITY = 0.0;

    private $currentPeriod;
    private $allWeapons;
    private $targetRanks;

    public function init()
    {
        parent::init();

        $this->currentPeriod = BattleHelper::calcPeriod2(
            (int)($_SERVER['REQUEST_TIME'] ?? time())
        );
        $this->allWeapons = ArrayHelper::map(
            Weapon2::find()->orderBy(['id' => SORT_ASC])->all(),
            'key',
            function (Weapon2 $w): Weapon2 {
                return $w;
            }
        );
        $this->targetRanks = ArrayHelper::getColumn(
            Rank2::find()->andWhere(['key' => ['s+', 'x']])->all(),
            'id'
        );
    }

    public function update(): void
    {
        foreach (Rule2::find()->orderBy(['id' => SORT_ASC])->all() as $rule) {
            $this->updateRule($rule);
        }
    }

    private function updateRule(Rule2 $rule): void
    {
        $startPeriod = $this->getCalcStartPeriod($rule);
        for ($p = $startPeriod; $p < $this->currentPeriod; ++$p) {
            $this->updatePeriod($rule, $p);
        }
    }

    private function updatePeriod(Rule2 $rule, int $period): void
    {
        vprintf("Updating Glicko2 rating for weapons (Splatoon2, %s, period %d)\n", [
            $rule->key,
            $period,
        ]);
        Yii::$app->db->transaction(function () use ($rule, $period): void {
            $glicko2 = new Glicko2();
            $version = $this->getVersionByPeriod($period);
            $prevRatings = $this->getRatings($rule, $version, $period - 1);
            $newRatings = [];
            foreach ($prevRatings as $k => $v) {
                $newRatings[$k] = clone $v;
            }
            $results4rating = [];
            foreach ($this->getBattles($rule, $period) as $battle) {
                $players = array_filter(
                    $battle->battlePlayersPure,
                    function (BattlePlayer2 $p): bool {
                        return !$p->isDisconnected && $p->weapon_id;
                    }
                );
                if (count($players) === 8) {
                    $winPlayers = array_values(array_filter(
                        $players,
                        function (BattlePlayer2 $p) use ($battle): bool {
                            return (bool)$battle->is_win === (bool)$p->is_my_team;
                        }
                    ));
                    $losePlayers = array_values(array_filter(
                        $players,
                        function (BattlePlayer2 $p) use ($battle): bool {
                            return (bool)$battle->is_win !== (bool)$p->is_my_team;
                        }
                    ));
                    $tasks = [
                        [$winPlayers, $losePlayers, 1],
                        [$losePlayers, $winPlayers, 0],
                    ];
                    foreach ($tasks as $task) {
                        foreach ($task[0] as $player1) {
                            $weapon1 = $player1->weapon->key;
                            if (!isset($results4rating[$weapon1])) {
                                $results4rating[$weapon1] = [];
                            }
                            foreach ($task[1] as $player2) {
                                $results4rating[$weapon1][] = new Result(
                                    $prevRatings[$weapon1],
                                    $task[2]
                                );
                            }
                        }
                    }
                }
            }

            foreach ($results4rating as $key => $results) {
                $newRatings[$key] = $glicko2->calculateRating($prevRatings[$key], $results);
            }

            $updatedRatings = array_filter($newRatings, function (Rating $rating): bool {
                return $rating->getRating() !== static::DEFAULT_RATING ||
                    $rating->getRatingDeviation() !== static::DEFAULT_RD;
            });

            if ($updatedRatings) {
                $inserts = array_map(
                    function (string $key, Rating $rating) use ($rule, $period): array {
                        return [
                            $rule->id,
                            $this->allWeapons[$key]->id,
                            $period,
                            $rating->getRating(),
                            $rating->getRatingDeviation(),
                        ];
                    },
                    array_keys($updatedRatings),
                    array_values($updatedRatings)
                );

                Weapon2RatingGlicko2::deleteAll([
                    'rule_id' => $rule->id,
                    'period' => $period,
                ]);
                Yii::$app->db
                    ->createCommand()
                    ->batchInsert(
                        Weapon2RatingGlicko2::tableName(),
                        ['rule_id', 'weapon_id', 'period', 'rating', 'deviation'],
                        $inserts
                    )
                    ->execute();
            }
        });
    }

    private function getCalcStartPeriod(Rule2 $rule): int
    {
        $value = (new Query())
            ->select([
                'max' => 'MAX([[period]])',
            ])
            ->from('weapon2_rating_glicko2')
            ->andWhere(['rule_id' => $rule->id])
            ->scalar();
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($value)) {
            // 1日分オーバーラップして更新する
            return min($this->currentPeriod, $value) - (24 / 2 + 1);
        }

        // 初回なので 1.0.0 以降のバージョンを探す
        $versions = array_filter(
            SplatoonVersion2::find()->all(),
            function (SplatoonVersion2 $v): bool {
                return version_compare($v->tag, '1.0.0', '>=');
            }
        );
        usort($versions, function (SplatoonVersion2 $a, SplatoonVersion2 $b): int {
            return version_compare($a->tag, $b->tag);
        });
        $version = array_shift($versions);
        return BattleHelper::calcPeriod2(strtotime($version->released_at)) + 1;
    }

    private function getVersionByPeriod(int $period): SplatoonVersion2
    {
        list($startAt, $endAt) = BattleHelper::periodToRange2DT($period);
        return SplatoonVersion2::find()
            ->andWhere(['<=', 'released_at', $startAt->format(DateTime::ATOM)])
            ->orderBy(['released_at' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    private function getRatings(Rule2 $rule, SplatoonVersion2 $reqVersion, int $period): array
    {
        $loaded = [];
        $version = $this->getVersionByPeriod($period);
        if ($version->group_id === $reqVersion->group_id) {
            $loaded = ArrayHelper::map(
                Weapon2RatingGlicko2::find()
                    ->andWhere([
                        'rule_id' => $rule->id,
                        'period' => $period,
                    ])
                    ->with('weapon')
                    ->all(),
                'weapon.key',
                function (Weapon2RatingGlicko2 $item): Rating {
                    return new Rating(
                        (float)$item->rating,
                        (float)$item->deviation,
                        static::VOLATILITY
                    );
                }
            );
        }

        return ArrayHelper::map(
            $this->allWeapons,
            'key',
            function (Weapon2 $weapon) use ($loaded): Rating {
                return $loaded[$weapon->key]
                    ?? new Rating(static::DEFAULT_RATING, static::DEFAULT_RD, static::VOLATILITY);
            }
        );
    }

    private function getBattles(Rule2 $rule, int $period)
    {
        $query = Battle2::find()
            ->orderBy(['battle2.id' => SORT_ASC])
            ->innerJoinWith(['lobby', 'mode'], false)
            ->with([
                'battlePlayersPure',
                'battlePlayersPure.weapon',
            ])
            ->andWhere(['and',
                [
                    'battle2.has_disconnect' => false,
                    'battle2.is_automated' => true,
                    'battle2.is_win' => [true, false],
                    'battle2.period' => $period,
                    'battle2.rule_id' => $rule->id,
                    'battle2.use_for_entire' => true,
                    'lobby2.key' => 'standard', // solo queue
                    'mode2.key' => ['regular', 'fest', 'gachi'], // non private
                ],
                ['not', ['battle2.map_id' => null]], // データがそれなりに存在することのチェック
                ['not', ['battle2.weapon_id' => null]], // 同上
                ['not', ['battle2.kill' => null]], // 同上
                ['not', ['battle2.death' => null]], // 同上
            ]);
        if ($rule->key !== 'nawabari') {
            $query->andWhere(['or',
                ['battle2.rank_id' => $this->targetRanks],
                ['battle2.rank_after_id' => $this->targetRanks],
            ]);
        }

        foreach ($query->each(100) as $row) {
            yield $row;
        }
    }
}
