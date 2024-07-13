<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function preg_replace_callback;

final class UserMiniInfoPowerValue extends Widget
{
    public ?float $value = null;

    public function run(): string
    {
        $value = $this->value;
        if ($value === null || $value < 0.0) {
            return Html::encode(Yii::t('app', 'N/A'));
        }

        $fmt = Yii::$app->formatter;
        $thousandSeparator = $fmt->thousandSeparator;

        try {
            $fmt->thousandSeparator = '';

            $text = $fmt->asDecimal(
                value: (float)$value,
                decimals: 1,
            );
        } finally {
            $fmt->thousandSeparator = $thousandSeparator;
        }

        if ($value < 100.0) {
            return Html::tag(
                'div',
                preg_replace_callback(
                    '/^(\d+[.,])(\d+)$/',
                    fn (array $match): string => implode('', [
                        Html::tag('span', Html::encode($match[1]), ['class' => 'text-muted']),
                        Html::tag('span', Html::encode($match[2]), ['class' => 'small text-muted']),
                    ]),
                    $text,
                ),
                [
                    'class' => 'scale-x',
                    'style' => [
                        '--scale-x' => '0.8',
                    ],
                ],
            );
        }

        return Html::tag(
            'div',
            preg_replace_callback(
                '/^(\d+?)(\d{2}[.,])(\d+)$/',
                fn (array $match): string => implode('', [
                    Html::tag('u', Html::encode($match[1]), ['class' => 'text-danger']),
                    Html::tag('span', Html::encode($match[2]), ['class' => 'text-muted']),
                    Html::tag('span', Html::encode($match[3]), ['class' => 'small text-muted']),
                ]),
                $text,
            ),
            [
                'class' => 'scale-x',
                'style' => [
                    '--scale-x' => '0.8',
                ],
            ],
        );
    }
}
