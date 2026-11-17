<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use Override;
use Yii;
use yii\base\Model;

use function time;

/**
 * Year-month selector for Splatoon 3 monthly-based user stats pages
 */
final class Spl3YearMonthForm extends Model
{
    /**
     * The first month that can be selected.
     * Splatfest World Premiere was held on 2022-08-27.
     */
    private const int LOWER_BOUND_YEAR = 2022;
    private const int LOWER_BOUND_MONTH = 8;

    /**
     * @var int|string|null
     */
    public $year;

    /**
     * @var int|string|null
     */
    public $month;

    public ?DateTimeZone $timeZone = null;

    /**
     * DO NOT SET MANUALLY.
     * It is made public for unit testing purposes only.
     */
    public ?DateTimeImmutable $now = null;

    #[Override]
    public function formName()
    {
        return '';
    }

    #[Override]
    public function rules()
    {
        return [
            [['year', 'month'], 'required'],
            [['year'], 'integer'],
            [['month'], 'integer', 'min' => 1, 'max' => 12],
            [['month'], 'validateRange'],
        ];
    }

    public function validateRange(string $attribute): void
    {
        if ($this->hasErrors('year') || $this->hasErrors('month')) {
            return;
        }

        $value = self::toMonthNumber((int)$this->year, (int)$this->month);
        $lower = self::toMonthNumber(self::LOWER_BOUND_YEAR, self::LOWER_BOUND_MONTH);
        $now = $this->getCurrentTimestamp();
        $upper = self::toMonthNumber((int)$now->format('Y'), (int)$now->format('n'));

        if ($value < $lower || $value > $upper) {
            $this->addError(
                $attribute,
                Yii::t('yii', '{attribute} is not in the allowed range.', [
                    'attribute' => $this->getAttributeLabel($attribute),
                ]),
            );
        }
    }

    public function getCurrentTimestamp(): DateTimeImmutable
    {
        if (!$this->now) {
            $this->now = new DateTimeImmutable()
                ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
                ->setTimezone($this->getTimeZone());
        }

        return $this->now;
    }

    /**
     * 00:00:00 of the first day of the selected month, in the form's time zone
     */
    public function getFirstDayOfMonth(): DateTimeImmutable
    {
        if ($this->hasErrors() || $this->year === null || $this->month === null) {
            throw new LogicException('The form is not validated');
        }

        return new DateTimeImmutable('now', $this->getTimeZone())
            ->setDate((int)$this->year, (int)$this->month, 1)
            ->setTime(0, 0, 0);
    }

    /**
     * 00:00:00 of the first day of the next month (exclusive upper bound of the selected month)
     */
    public function getFirstDayOfNextMonth(): DateTimeImmutable
    {
        return $this->getFirstDayOfMonth()->modify('first day of next month');
    }

    /**
     * The first day of the previous month, or null if it is out of the selectable range
     */
    public function getPrevMonth(): ?DateTimeImmutable
    {
        $prev = $this->getFirstDayOfMonth()->modify('first day of previous month');
        $lower = self::toMonthNumber(self::LOWER_BOUND_YEAR, self::LOWER_BOUND_MONTH);

        return self::toMonthNumber((int)$prev->format('Y'), (int)$prev->format('n')) >= $lower
            ? $prev
            : null;
    }

    /**
     * The first day of the next month, or null if it is in the future
     */
    public function getNextMonth(): ?DateTimeImmutable
    {
        $next = $this->getFirstDayOfNextMonth();
        $now = $this->getCurrentTimestamp();

        return self::toMonthNumber((int)$next->format('Y'), (int)$next->format('n'))
            <= self::toMonthNumber((int)$now->format('Y'), (int)$now->format('n'))
            ? $next
            : null;
    }

    private function getTimeZone(): DateTimeZone
    {
        if (!$this->timeZone) {
            $this->timeZone = new DateTimeZone(Yii::$app->timeZone);
        }

        return $this->timeZone;
    }

    private static function toMonthNumber(int $year, int $month): int
    {
        return $year * 12 + $month - 1;
    }
}
