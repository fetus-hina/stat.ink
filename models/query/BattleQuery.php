<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\query;

use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\BattleSummarizer;
use app\components\helpers\Resource;
use app\models\BattleFilterForm;
use app\models\BattleImageType;
use app\models\SplatoonVersion;
use app\models\Timezone;
use app\models\Weapon;
use stdClass;
use yii\db\ActiveQuery;

use function date;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function gmdate;
use function is_scalar;
use function mktime;
use function preg_match;
use function strtotime;
use function substr;
use function time;
use function trim;

class BattleQuery extends ActiveQuery
{
    public function hasResultImage(): self
    {
        return $this->innerJoinWith(
            [
                'battleImages' => function (ActiveQuery $query): void {
                    $query->onCondition(['{{battle_image}}.[[type_id]]' => BattleImageType::ID_RESULT]);
                },
            ],
            false,
        );
    }

    public function filter(BattleFilterForm $filter): self
    {
        return $this
            ->filterByScreenName($filter->screen_name)
            ->filterByLobby($filter->lobby)
            ->filterByRule($filter->rule)
            ->filterByMap($filter->map)
            ->filterByWeapon($filter->weapon)
            ->filterByRank($filter->rank)
            ->filterByResult($filter->result)
            ->filterByTerm($filter->term, [
                'filter' => $filter,
                'from' => $filter->term_from,
                'to' => $filter->term_to,
                'tz' => $filter->timezone,
            ])
            ->filterByIdRange($filter->id_from, $filter->id_to);
    }

    public function filterByScreenName(?string $value): self
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        return $this->innerJoinWith('user')->andWhere(['{{user}}.[[screen_name]]' => $value]);
    }

    public function filterByLobby(?string $value): self
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('lobby');
        $this->andWhere(['{{lobby}}.[[key]]' => $value]);
        return $this;
    }

    public function filterByRule(?string $value): self
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('rule');
        if (substr($value, 0, 1) === '@') {
            $this->innerJoinWith('rule.mode');
            $this->andWhere(['{{game_mode}}.[[key]]' => substr($value, 1)]);
        } else {
            $this->andWhere(['{{rule}}.[[key]]' => $value]);
        }
        return $this;
    }

    public function filterByMap(?string $value): self
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        return $this->innerJoinWith('map')->andWhere(['{{map}}.[[key]]' => $value]);
    }

    public function filterByWeapon(?string $value): self
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('weapon');
        switch (substr($value, 0, 1)) {
            default:
                $this->andWhere(['{{weapon}}.[[key]]' => $value]);
                break;

            case '@':
                $this->innerJoinWith('weapon.type');
                $this->andWhere(['{{weapon_type}}.[[key]]' => substr($value, 1)]);
                break;

            case '+':
                $this->innerJoinWith('weapon.subweapon');
                $this->andWhere(['{{subweapon}}.[[key]]' => substr($value, 1)]);
                break;

            case '*':
                $this->innerJoinWith('weapon.special');
                $this->andWhere(['{{special}}.[[key]]' => substr($value, 1)]);
                break;

            case '~':
                if (!$main = Weapon::findOne(['key' => substr($value, 1)])) {
                    $this->andWhere('1 = 0');
                } else {
                    $this->andWhere(['{{weapon}}.[[main_group_id]]' => $main->id]);
                }
                break;
        }
        return $this;
    }

    public function filterByRank(?string $rank): self
    {
        if (substr($rank, 0, 1) === '~') {
            $this->innerJoinWith(['rank', 'rank.group']);
            $this->andWhere(['{{rank_group}}.[[key]]' => substr($rank, 1)]);
        } elseif ($rank != '') {
            $this->innerJoinWith('rank');
            $this->andWhere(['{{rank}}.[[key]]' => $rank]);
        }
        return $this;
    }

    /**
     * @param string|bool|null $result
     */
    public function filterByResult($result): self
    {
        if ($result === 'win' || $result === true) {
            $this->andWhere(['{{battle}}.[[is_win]]' => true]);
        } elseif ($result === 'lose' || $result === false) {
            $this->andWhere(['{{battle}}.[[is_win]]' => false]);
        }
        return $this;
    }

    public function filterByTerm(?string $value, array $options = []): self
    {
        $raii = null;
        try {
            $now = (int)($_SERVER['REQUEST_TIME'] ?? time());
            $currentPeriod = BattleHelper::calcPeriod($now);

            // 指定されたタイムゾーンで処理する
            // この関数を抜けると元のタイムゾーンに戻る
            $tzIdent = @$options['tz'] ?? Yii::$app->timeZone;
            if (!is_scalar($tzIdent) || !$tzModel = Timezone::findOne(['identifier' => $tzIdent])) {
                $tzModel = Timezone::findOne(['identifier' => Yii::$app->timeZone]);
            }
            if ($tzModel) {
                $oldTz = date_default_timezone_get();
                $raii = new Resource(true, function () use ($oldTz): void {
                    date_default_timezone_set($oldTz);
                });
                date_default_timezone_set($tzModel->identifier);
            }

            switch ($value) {
                case 'this-period':
                    $this->andWhere(['{{battle}}.[[period]]' => $currentPeriod]);
                    break;

                case 'last-period':
                    $this->andWhere(['{{battle}}.[[period]]' => $currentPeriod - 1]);
                    break;

                case '24h':
                    $this->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $now - 86400)]);
                    break;

                case 'today':
                    $t = mktime(0, 0, 0, date('n', $now), date('j', $now), date('Y', $now));
                    $this->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                    break;

                case 'yesterday':
                    // 昨日の 00:00:00
                    $t1 = mktime(0, 0, 0, date('n', $now), date('j', $now) - 1, date('Y', $now));
                    // 今日の 00:00:00
                    $t2 = mktime(0, 0, 0, date('n', $now), date('j', $now), date('Y', $now));
                    $this->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t1)]);
                    $this->andWhere(['<', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t2)]);
                    break;

                case 'term':
                    if (isset($options['from']) && $options['from'] != '') {
                        if ($t = @strtotime($options['from'])) {
                            $this->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                        }
                    }
                    if (isset($options['to']) && $options['to'] != '') {
                        if ($t = @strtotime($options['to'])) {
                            $this->andWhere(['<=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                        }
                    }
                    break;

                default:
                    if (isset($options['filter']) && preg_match('/^last-(\d+)-battles$/', $value, $match)) {
                        $range = BattleHelper::getNBattlesRange($options['filter'], (int)$match[1]);
                        if (!$range || $range['min_id'] < 1 || $range['max_id'] < 1) {
                            $this->andWhere('1 <> 1'); // Always false
                        } else {
                            $this->andWhere(['between', '{{battle}}.[[id]]', $range['min_id'], $range['max_id']]);
                        }
                    } elseif (preg_match('/^v\d+/', $value)) {
                        $version = SplatoonVersion::findOne(['tag' => substr($value, 1)]);
                        if (!$version) {
                            $this->andWhere('1 <> 1'); // Always false
                        } else {
                            $this->andWhere(['{{battle}}.[[version_id]]' => $version->id]);
                        }
                    }
                    break;
            }

            return $this;
        } finally {
            unset($raii);
        }
    }

    public function filterByIdRange(?int $idFrom, ?int $idTo): self
    {
        if ($idFrom != '' && $idFrom > 0) {
            $this->andWhere(['>=', '{{battle}}.[[id]]', (int)$idFrom]);
        }
        if ($idTo != '' && $idTo > 0) {
            $this->andWhere(['<=', '{{battle}}.[[id]]', (int)$idTo]);
        }
        return $this;
    }

    public function getSummary(): stdClass
    {
        return BattleSummarizer::getSummary($this);
    }
}
