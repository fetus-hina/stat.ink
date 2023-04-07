<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\salmon3FilterForm;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Throwable;
use Yii;
use app\models\Map3;
use app\models\Salmon3FilterForm;
use app\models\SalmonMap3;
use yii\db\ActiveQuery;

use function is_string;
use function trim;

trait QueryDecoratorTrait
{
    public function decorateQuery(ActiveQuery $query): void
    {
        if ($this->hasErrors()) {
            Yii::warning('This form has errors', __METHOD__);
            $query->andWhere('1 <> 1'); // make no results
            return;
        }

        $this->decorateLobbyFilter($query, $this->lobby);
        $this->decorateMapFilter($query, $this->map);
        $this->decorateResultFilter($query, $this->result);
        $this->decorateTermFilter($query, $this->term);
    }

    private function decorateLobbyFilter(ActiveQuery $query, ?string $key): void
    {
        $key = trim((string)$key);
        switch ($key) {
            case Salmon3FilterForm::LOBBY_ALL:
            case '':
                return;

            case Salmon3FilterForm::LOBBY_BIG_RUN:
                $query->andWhere([
                    '{{%salmon3}}.[[is_big_run]]' => true,
                    '{{%salmon3}}.[[is_private]]' => false,
                ]);
                return;

            case Salmon3FilterForm::LOBBY_EGGSTRA_WORK:
                $query->andWhere([
                    '{{%salmon3}}.[[is_eggstra_work]]' => true,
                    '{{%salmon3}}.[[is_private]]' => false,
                ]);
                return;

            case Salmon3FilterForm::LOBBY_NORMAL:
                $query->andWhere([
                    '{{%salmon3}}.[[is_big_run]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ]);
                return;

            case Salmon3FilterForm::LOBBY_NOT_PRIVATE:
                $query->andWhere(['{{%salmon3}}.[[is_private]]' => false]);
                return;

            case Salmon3FilterForm::LOBBY_PRIVATE:
                $query->andWhere(['{{%salmon3}}.[[is_private]]' => true]);
                return;

            default:
                $query->andWhere('1 <> 1');
        }
    }

    private function decorateMapFilter(ActiveQuery $query, ?string $key): void
    {
        $key = trim((string)$key);
        if ($key === '') {
            return;
        }

        $salmonMap = SalmonMap3::find()
            ->andWhere(['key' => $key])
            ->cache(600)
            ->limit(1)
            ->one();
        if ($salmonMap) {
            $query->andWhere(['{{%salmon3}}.[[stage_id]]' => $salmonMap->id]);
            return;
        }

        $map = Map3::find()
            ->andWhere(['key' => $key])
            ->cache(600)
            ->limit(1)
            ->one();
        if ($map) {
            $query->andWhere(['{{%salmon3}}.[[big_stage_id]]' => $map->id]);
            return;
        }

        $query->andWhere('1 <> 1');
    }

    private function decorateResultFilter(ActiveQuery $query, ?string $key): void
    {
        $key = trim((string)$key);
        if ($key === '') {
            return;
        }

        switch ($key) {
            case Salmon3FilterForm::RESULT_CLEARED:
                $query->andWhere(['or',
                    [
                        '{{%salmon3}}.[[clear_waves]]' => 3, // no-eggstra
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    ],
                    [
                        '{{%salmon3}}.[[clear_waves]]' => 5,
                        '{{%salmon3}}.[[is_eggstra_work]]' => true,
                    ],
                ]);
                break;

            case Salmon3FilterForm::RESULT_CLEARED_KING_APPEAR:
                $query->andWhere(['and',
                    [
                        '{{%salmon3}}.[[clear_waves]]' => 3,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    ],
                    ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                ]);
                break;

            case Salmon3FilterForm::RESULT_CLEARED_KING_DEFEAT:
                $query->andWhere(['and',
                    [
                        '{{%salmon3}}.[[clear_extra]]' => true,
                        '{{%salmon3}}.[[clear_waves]]' => 3,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    ],
                    ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                ]);
                break;

            case Salmon3FilterForm::RESULT_CLEARED_KING_FAILED:
                $query->andWhere(['and',
                    [
                        '{{%salmon3}}.[[clear_extra]]' => false,
                        '{{%salmon3}}.[[clear_waves]]' => 3,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    ],
                    ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                ]);
                break;

            case Salmon3FilterForm::RESULT_FAILED:
                $query->andWhere(['or',
                    ['and',
                        ['{{%salmon3}}.[[is_eggstra_work]]' => false],
                        ['<', '{{%salmon3}}.[[clear_waves]]', 3], // no eggstra
                    ],
                    ['and',
                        ['{{%salmon3}}.[[is_eggstra_work]]' => true],
                        ['<', '{{%salmon3}}.[[clear_waves]]', 5],
                    ],
                ]);
                break;

            case Salmon3FilterForm::RESULT_FAILED_W1:
                $query->andWhere(['{{%salmon3}}.[[clear_waves]]' => 0]);
                break;

            case Salmon3FilterForm::RESULT_FAILED_W2:
                $query->andWhere(['{{%salmon3}}.[[clear_waves]]' => 1]);
                break;

            case Salmon3FilterForm::RESULT_FAILED_W3:
                $query->andWhere(['{{%salmon3}}.[[clear_waves]]' => 2]);
                break;

            case Salmon3FilterForm::RESULT_FAILED_W4:
                $query->andWhere([
                    '{{%salmon3}}.[[clear_waves]]' => 3,
                    '{{%salmon3}}.[[is_eggstra_work]]' => true,
                ]);
                break;

            case Salmon3FilterForm::RESULT_FAILED_W5:
                $query->andWhere([
                    '{{%salmon3}}.[[clear_waves]]' => 4,
                    '{{%salmon3}}.[[is_eggstra_work]]' => true,
                ]);
                break;

            default:
                $query->andWhere('1 <> 1');
        }
    }

    private function decorateTermFilter(ActiveQuery $query, ?string $key): void
    {
        $key = trim((string)$key);
        if ($key === '') {
            return;
        }

        match ($key) {
            'term' => $this->decorateSpecifiedTerm($query),
            default => $query->andWhere('1 <> 1'),
        };
    }

    private function decorateSpecifiedTerm(ActiveQuery $query): void
    {
        $from = self::normalizeTermDatetime($this->term_from);
        $to = self::normalizeTermDatetime($this->term_to);
        if (!$from || !$to) {
            $query->andWhere('1 <> 1');
            return;
        }

        $query->andWhere(
            ['between',
                '{{%salmon3}}.[[start_at]]',
                $from->format(DateTimeInterface::ATOM),
                $to->format(DateTimeInterface::ATOM),
            ],
        );
    }

    private function normalizeTermDatetime(mixed $value): ?DateTimeInterface
    {
        if (!is_string($value)) {
            return null;
        }

        try {
            $tz = new DateTimeZone(Yii::$app->timeZone);
            return (new DateTimeImmutable($value, $tz))->setTimezone($tz);
        } catch (Throwable $e) {
            return null;
        }
    }
}
