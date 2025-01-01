<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\base\Model;
use yii\validators\InlineValidator;

use function substr;
use function time;

final class Spl2YearMonthForm extends Model
{
    private const SPLATOON2_RELEASE_DATE = '2017-07-21';

    public $year;
    public $month;

    public ?DateTimeZone $timeZone = null;

    /**
     * DO NOT SET MANUALLY.
     * It is made public for unit testing purposes only.
     */
    public ?DateTimeImmutable $now = null;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        $now = $this->getCurrentTimestamp();

        return [
            [['year', 'month'], 'required'],
            [['year'], 'integer',
                'min' => (int)substr(self::SPLATOON2_RELEASE_DATE, 0, 4),
                'max' => (int)$now->format('Y'),
            ],
            [['month'], 'integer',
                'min' => 1,
                'max' => 12,
            ],
            [['month'], 'validateYMCombination'],
        ];
    }

    /**
     * @param mixed $params
     * @param mixed $current
     */
    public function validateYMCombination(
        string $attribute,
        $params,
        InlineValidator $validator,
        $current,
    ): void {
        if ($this->hasErrors('year') || $this->hasErrors('month')) {
            return;
        }

        if ((int)$this->year === (int)substr(self::SPLATOON2_RELEASE_DATE, 0, 4)) {
            if ((int)$this->month < (int)substr(self::SPLATOON2_RELEASE_DATE, 5, 2)) {
                $this->addError(
                    $attribute,
                    Yii::t('yii', '{attribute} is not in the allowed range.', [
                        'attribute' => $attribute,
                    ]),
                );
                return;
            }
        }

        $now = $this->getCurrentTimestamp();
        if ((int)$this->year === (int)$now->format('Y')) {
            if ((int)$this->month > (int)$now->format('n')) {
                $this->addError(
                    $attribute,
                    Yii::t('yii', '{attribute} is not in the allowed range.', [
                        'attribute' => $attribute,
                    ]),
                );
                return;
            }
        }
    }

    public function getCurrentTimestamp(): DateTimeImmutable
    {
        if (!$this->now) {
            if (!$this->timeZone) {
                $this->timeZone = new DateTimeZone(Yii::$app->timeZone);
            }

            $this->now = (new DateTimeImmutable())
                ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
                ->setTimezone($this->timeZone);
        }

        return $this->now;
    }
}
