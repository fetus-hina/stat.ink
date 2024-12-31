<?php

/**
 * @copyright Copyright (C) 2020-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\BlogEntry;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction;

use function time;

class BlogEntryAction extends ViewAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        $f = Yii::$app->formatter;
        $now = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $tz = new DateTimeZone('Etc/UTC');

        return ArrayHelper::getColumn(
            BlogEntry::find()->latest()->limit(3)->all(),
            function (BlogEntry $entry) use ($f, $now, $tz): array {
                $at = (new DateTimeImmutable($entry->at))->setTimezone($tz);

                return [
                    'id' => $entry->uuid,
                    'title' => $entry->title,
                    'url' => $entry->url,
                    'at' => [
                        'time' => $at->getTimestamp(),
                        'iso8601' => $at->format(DateTime::ATOM),
                        'natural' => $f->asDatetime($at, 'medium'),
                        'relative' => $f->asRelativeTime($at, $now),
                    ],
                ];
            },
        );
    }
}
