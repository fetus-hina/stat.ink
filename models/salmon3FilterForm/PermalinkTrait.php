<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\salmon3FilterForm;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;

use function is_string;
use function sprintf;
use function trim;

trait PermalinkTrait
{
    public function toPermLink(string|false $formName = false)
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
            'map',
            'result',
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

            match ($termValue) {
                'term' => $this->permaLinkTerm($pushTerm),
                default => $push($key, $value),
            };
        }

        return $ret;
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
}
