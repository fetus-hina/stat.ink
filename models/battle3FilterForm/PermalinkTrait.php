<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Season3;

use function floor;
use function is_string;
use function sprintf;
use function strtotime;
use function substr;
use function trim;

trait PermalinkTrait
{
    /**
     * @param string|false $formName
     */
    public function toPermLink($formName = false)
    {
        $formName = trim(is_string($formName) ? $formName : $this->formName());

        $ret = [];
        $push = function (string $key, string $value) use ($formName, &$ret): void {
            if ($formName !== '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }

            $ret[$key] = $value;
        };

        $copyKeys = [
            'lobby',
            'rule',
            'map',
            'weapon',
            'result',
            'knockout',
        ];
        foreach ($copyKeys as $key) {
            $value = trim((string)$this->$key);
            if ($value !== '') {
                $push($key, $value);
            }
        }

        $termValue = trim((string)$this->term);
        if ($termValue !== '') {
            $pushTerm = function (int $from, int $to) use ($push): void {
                $push('term', 'term');
                $push('term_from', "@{$from}");
                $push('term_to', "@{$to}");
            };

            match (substr($termValue, 0, 1)) {
                '@' => $this->permaLinkSeason($pushTerm, substr($termValue, 1)),
                default => match ($termValue) {
                    '24h' => $this->permaLink24H($pushTerm),
                    'last-period' => $this->permaLinkPeriod($pushTerm, -1),
                    'term' => $this->permaLinkTerm($pushTerm),
                    'this-period' => $this->permaLinkPeriod($pushTerm, 0),
                    'today' => $this->permaLinkDay($pushTerm, 0),
                    'yesterday' => $this->permaLinkDay($pushTerm, -1),
                    default => $push($key, $value),
                },
            };
        }

        return $ret;
    }

    /**
     * @param callable(int, int): void $push
     */
    private function permaLink24H(callable $push): void
    {
        $now = $_SERVER['REQUEST_TIME'];
        $push($now - 86400, $now - 1);
    }

    /**
     * @param callable(int, int): void $push
     */
    private function permaLinkPeriod(callable $push, int $offset): void
    {
        $currentPeriod = (int)floor($_SERVER['REQUEST_TIME'] / 7200);
        $targetPeriod = $offset + $currentPeriod;
        $push(
            $targetPeriod * 7200,
            ($targetPeriod + 1) * 7200 - 1,
        );
    }

    /**
     * @param callable(int, int): void $push
     */
    private function permaLinkSeason(callable $push, string $key): void
    {
        $season = Season3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if ($season) {
            $push(
                TypeHelper::int(strtotime($season->start_at)),
                TypeHelper::int(strtotime($season->end_at)) - 1,
            );
        }
    }

    /**
     * @param callable(int, int): void $push
     */
    private function permaLinkTerm(callable $push): void
    {
        try {
            $tz = new DateTimeZone(Yii::$app->timeZone);

            $date1 = new DateTimeImmutable($this->term_from, $tz);
            $date2 = new DateTimeImmutable($this->term_to, $tz);
            $push(
                $date1->getTimestamp(),
                $date2->getTimestamp(),
            );
        } catch (Throwable $e) {
        }
    }

    /**
     * @param callable(int, int): void $push
     */
    private function permaLinkDay(callable $push, int $offset): void
    {
        $now = (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone)))
            ->setTimestamp($_SERVER['REQUEST_TIME']);
        $today = $now->setTime(0, 0, 0);
        $target = match (true) {
            $offset < 0 => $today->sub(new DateInterval(sprintf('P%dD', -1 * $offset))),
            $offset > 0 => $today->add(new DateInterval(sprintf('P%dD', $offset))),
            default => $today,
        };

        $push(
            $target->getTimestamp(),
            $target->getTimestamp() + 86400 - 1,
        );
    }
}
