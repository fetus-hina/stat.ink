<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\query;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\BattleSummarizer;
use app\models\Battle2FilterForm;
use app\models\Map2;
use app\models\Special2;
use app\models\SplatoonVersion2;
use app\models\Subweapon2;
use app\models\Timezone;
use app\models\User;
use app\models\Weapon2;
use app\models\WeaponType2;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

final class Battle2Query extends ActiveQuery
{
    public function withFreshness(): self
    {
        [, $alias] = $this->getTableNameAndAlias();
        if (!$this->select) {
            $this->select = ["{$alias}.*"];
        }
        $this->select['freshness_id'] = 'freshness2.id';
        $this->join[] = [
            'LEFT JOIN',
            'freshness2',
            "{$alias}.freshness <@ freshness2.range",
        ];
        return $this;
    }

    public function applyFilter(Battle2FilterForm $form): self
    {
        $and = ['and'];
        if ($form->screen_name != '') {
            $this->innerJoinWith('user');
            $and[] = ['{{user}}.[[screen_name]]' => $form->screen_name];
        }
        if ($form->rule != '') {
            $parts = explode('-', $form->rule);
            if (count($parts) === 3) {
                $this->innerJoinWith(['lobby', 'mode', 'rule']);
                switch ($parts[0]) {
                    case 'any':
                        break;

                    case 'any_squad':
                        $and[] = ['lobby2.key' => ['squad_2', 'squad_4']];
                        break;

                    default:
                        $and[] = ['lobby2.key' => $parts[0]];
                        break;
                }
                $and[] = ['mode2.key' => $parts[1]];
                switch ($parts[2]) {
                    case 'any':
                        break;

                    case 'gachi':
                        $and[] = ['rule2.key' => ['area', 'yagura', 'hoko', 'asari']];
                        break;

                    default:
                        $and[] = ['rule2.key' => $parts[2]];
                        break;
                }
            }
        }
        if ($form->map != '') {
            if ($form->map !== 'mystery') {
                $this->innerJoinWith(['map']);
                $and[] = ['{{map2}}.[[key]]' => (string)$form->map];
            } else {
                $and[] = [
                    '{{battle2}}.[[map_id]]' => ArrayHelper::getColumn(
                        Map2::find()
                            ->andWhere(['like', 'key', 'mystery%', false])
                            ->asArray()
                            ->all(),
                        'id',
                    ),
                ];
            }
        }
        if ($form->weapon != '') {
            switch (substr($form->weapon, 0, 1)) {
                default:
                    $weapon = Weapon2::findOne(['key' => $form->weapon]);
                    $and[] = ['battle2.weapon_id' => $weapon->id];
                    break;

                case '@': // type
                    $this->innerJoinWith('weapon');
                    $type = WeaponType2::findOne(['key' => substr($form->weapon, 1)]);
                    $and[] = ['weapon2.type_id' => $type->id];
                    break;

                case '~': // main weapon
                    $this->innerJoinWith('weapon');
                    $main = Weapon2::findOne(['key' => substr($form->weapon, 1)]);
                    $and[] = ['weapon2.main_group_id' => $main->id];
                    break;

                case '+': // sub weapon
                    $this->innerJoinWith('weapon');
                    $sub = Subweapon2::findOne(['key' => substr($form->weapon, 1)]);
                    $and[] = ['weapon2.subweapon_id' => $sub->id];
                    break;

                case '*': // special
                    $this->innerJoinWith('weapon');
                    $sp = Special2::findOne(['key' => substr($form->weapon, 1)]);
                    $and[] = ['weapon2.special_id' => $sp->id];
                    break;
            }
        }
        if ($form->rank != '') {
            $this->innerJoinWith(['rank', 'rank.group']);
            if (substr($form->rank, 0, 1) === '~') { // group
                $and[] = ['rank_group2.key' => substr($form->rank, 1)];
            } else {
                $and[] = ['rank2.key' => $form->rank];
            }
        }
        if ($form->result != '' || is_bool($form->result)) {
            $and[] = [
                'battle2.is_win' => ($form->result === 'win' || $form->result === true),
            ];
        }
        if ($form->has_disconnect != '' || is_bool($form->has_disconnect)) {
            $value = $form->has_disconnect === 'yes' || $form->has_disconnect === true;
            $and[] = ['battle2.has_disconnect' => $value];
        }
        if ($form->id_from != '' && $form->id_from > 0) {
            $and[] = ['>=', 'battle2.id', (int)$form->id_from];
        }
        if ($form->id_to != '' && $form->id_to > 0) {
            $and[] = ['<=', 'battle2.id', (int)$form->id_to];
        }
        if ($form->filterTeam) {
            $and[] = ['battle2.my_team_id' => $form->filterTeam];
        }
        if ($form->filterIdRange) {
            $and[] = ['between',
                'battle2.id',
                (int)$form->filterIdRange[0],
                (int)$form->filterIdRange[1],
            ];
        }
        if ($form->filterPeriod) {
            $and[] = ['between',
                'battle2.period',
                (int)$form->filterPeriod[0],
                (int)$form->filterPeriod[1],
            ];
        }
        if ($form->filterWithPrincipalId) {
            $this->innerJoinWith('battlePlayersPure');
            $and[] = ['{{battle_player2}}.[[splatnet_id]]' => $form->filterWithPrincipalId];

            if (in_array((string)$form->with_team, ['good', 'bad'], true)) {
                $and[] = [
                    '{{battle_player2}}.[[is_my_team]]' => $form->with_team === 'good',
                ];
            }
        }
        if (count($and) > 1) {
            $this->andWhere($and);
        }
        if ($form->term != '') {
            $this->filterTerm($form->term, [
                'from' => $form->term_from,
                'to' => $form->term_to,
                'filter' => $form,
                'timeZone' => $form->timezone,
            ]);
        }

        return $this;
    }

    public function filterTerm(string $term, array $options): self
    {
        // DateTimeZone
        $tz = (function (?string $tzIdent) {
            if ($tzIdent && ($model = Timezone::findOne(['identifier' => (string)$tzIdent]))) {
                return new DateTimeZone($model->identifier);
            }
            return new DateTimeZone(Yii::$app->timeZone);
        })($options['timeZone'] ?? null);

        // DateTimeImmutable
        $now = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTimezone($tz);
        $currentPeriod = BattleHelper::calcPeriod2($now->getTimestamp());
        $date = sprintf('(CASE %s END)::timestamp with time zone', implode(' ', [
            'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
            "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
            'WHEN {{battle2}}.[[period]] IS NOT NULL THEN PERIOD2_TO_TIMESTAMP({{battle2}}.[[period]])',
            "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
        ]));

        switch ($term) {
            case 'this-period':
                $this->andWhere(['battle2.period' => $currentPeriod]);
                break;

            case 'last-period':
                $this->andWhere(['battle2.period' => $currentPeriod - 1]);
                break;

            case '24h':
                $t = $now->sub(new DateInterval('PT24H'));
                $this->andWhere(
                    ['>', $date, $t->format(DateTime::ATOM)]
                );
                break;

            case 'today':
                $today = $now->setTime(0, 0, 0);
                $tomorrow = $today->add(new DateInterval('P1D'));
                $this->andWhere(['and',
                    ['>=', $date, $today->format(DateTime::ATOM)],
                    ['<', $date, $tomorrow->format(DateTime::ATOM)],
                ]);
                break;

            case 'yesterday':
                $today = $now->setTime(0, 0, 0);
                $yesterday = $today->sub(new DateInterval('P1D'));
                $this->andWhere(['and',
                    ['>=', $date, $yesterday->format(DateTime::ATOM)],
                    ['<', $date, $today->format(DateTime::ATOM)],
                ]);
                break;

            case 'this-month-utc':
                $utcNow = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setTimestamp($now->getTimestamp());
                $thisMonth = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setDate((int)$utcNow->format('Y'), (int)$utcNow->format('n'), 1)
                    ->setTime(0, 0, 0);
                $this->andWhere([
                    'between',
                    'battle2.period',
                    BattleHelper::calcPeriod2($thisMonth->getTimestamp()),
                    BattleHelper::calcPeriod2($now->getTimestamp()),
                ]);
                break;

            case 'last-month-utc':
                $utcNow = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setTimestamp($now->getTimestamp());

                $lastMonthPeriod = BattleHelper::calcPeriod2(
                    (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone('Etc/UTC'))
                        ->setDate((int)$utcNow->format('Y'), (int)$utcNow->format('n') - 1, 1)
                        ->setTime(0, 0, 0)
                        ->getTimestamp()
                );

                $thisMonthPeriod = BattleHelper::calcPeriod2(
                    (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone('Etc/UTC'))
                        ->setDate((int)$utcNow->format('Y'), (int)$utcNow->format('n'), 1)
                        ->setTime(0, 0, 0)
                        ->getTimestamp()
                );

                $this->andWhere(['and',
                    ['>=', 'battle2.period', $lastMonthPeriod],
                    ['<', 'battle2.period', $thisMonthPeriod],
                ]);
                break;

            case 'this-fest':
                try {
                    if (!$form = $options['filter']) {
                        throw new Exception();
                    }

                    if (!$user = User::findOne(['screen_name' => $form->screen_name])) {
                        throw new Exception();
                    }

                    if (!$range = BattleHelper::getLastPlayedSplatfestPeriodRange2($user)) {
                        throw new Exception();
                    }

                    $this->andWhere(['between', 'battle2.period', $range[0], $range[1]]);
                } catch (Throwable $e) {
                    $this->andWhere('0 = 1');
                }
                break;

            case 'term':
                try {
                    $from = ($options['from'] ?? '') != ''
                        ? (new DateTimeImmutable($options['from']))->setTimezone($tz)
                        : null;
                    $to = ($options['to'] ?? '') != ''
                        ? (new DateTimeImmutable($options['to']))->setTimezone($tz)
                        : null;
                    if ($from) {
                        $this->andWhere(
                            ['>=', $date, $from->format(DateTime::ATOM)]
                        );
                    }
                    if ($to) {
                        $this->andWhere(
                            ['<', $date, $to->format(DateTime::ATOM)]
                        );
                    }
                } catch (Throwable $e) {
                }
                break;

            default:
                if (
                    isset($options['filter']) &&
                    preg_match('/^last-(\d+)-battles$/', $term, $match)
                ) {
                    $range = BattleHelper::getNBattlesRange2(
                        $options['filter'],
                        (int)$match[1]
                    );
                    if ($range && $range['min_id'] && $range['max_id']) {
                        $this->andWhere([
                            'between',
                            'battle2.id',
                            (int)$range['min_id'],
                            (int)$range['max_id'],
                        ]);
                    }
                } elseif (preg_match('/^last-(\d+)-periods$/', $term, $match)) {
                    $currentPeriod = BattleHelper::calcPeriod2($now->getTimestamp());
                    $this->andWhere([
                        'between',
                        'battle2.period',
                        $currentPeriod - $match[1] + 1,
                        $currentPeriod,
                    ]);
                } elseif (preg_match('/^~?v\d+/', $term)) {
                    $versions = (function () use ($term) {
                        $query = SplatoonVersion2::find();
                        if (substr($term, 0, 1) === '~') {
                            $query->innerJoinWith('group', false)
                                ->andWhere([
                                    'splatoon_version_group2.tag' => substr($term, 2),
                                ]);
                        } else {
                            $query->andWhere(['tag' => substr($term, 1)]);
                        }
                        return array_map(
                            fn (SplatoonVersion2 $version): int => $version->id,
                            $query->all(),
                        );
                    })();
                    if (!$versions) {
                        $this->andWhere('1 <> 1'); // Always false
                    } else {
                        $this->andWhere(['battle2.version_id' => $versions]);
                    }
                }
                break;
        }
        return $this;
    }

    public function getSummary()
    {
        return BattleSummarizer::getSummary2($this);
    }
}
