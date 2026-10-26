<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Override;
use Yii;
use app\components\helpers\TypeHelper;
use app\components\i18n\Formatter;
use yii\base\Widget;
use yii\helpers\Html;

use function array_filter;
use function implode;
use function is_array;

class TimestampColumnWidget extends Widget
{
    public mixed $value = null;
    public bool $showTZ = true;
    public bool $showRelative = false;
    /**
     * @var Formatter|array{class: class-string<Formatter>, ...}|null
     */
    public $formatter;

    #[Override]
    public function init(): void
    {
        parent::init();

        if (!$this->formatter) {
            $this->formatter = clone Yii::$app->formatter;
            $this->formatter->nullDisplay = '';
        } elseif (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
    }

    public function run()
    {
        $f = TypeHelper::instanceOf($this->formatter, Formatter::class);

        if ($this->value === null) {
            return $f->asText(null);
        }

        return implode(' ', array_filter([
            $f->asHtmlDatetime($this->value),
            $this->showTZ
                ? Html::a(
                    Html::encode(
                        new DateTimeImmutable()
                            ->setTimestamp((int)$f->asTimestamp($this->value))
                            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
                            ->format('T'),
                    ),
                    '#timezone-dialog',
                    [
                        'role' => 'button',
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'false',
                        'data' => [
                            'toggle' => 'modal',
                        ],
                    ],
                )
                : null,
            $this->showRelative
                ? Html::encode('(' . $f->asRelativeTime($this->value) . ')')
                : null,
        ]));
    }
}
