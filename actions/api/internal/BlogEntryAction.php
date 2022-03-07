<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use app\models\BlogEntry;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction;

use const SORT_DESC;

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
            BlogEntry::find()
                ->orderBy([
                    '{{blog_entry}}.[[at]]' => SORT_DESC,
                ])
                ->limit(3)
                ->all(),
            function (BlogEntry $entry) use ($f, $now, $tz): array {
                $at = (new DateTimeImmutable($entry->at))
                    ->setTimezone($tz);

                return [
                    'id' => $entry->uuid,
                    'title' => $entry->title,
                    'url' => $entry->url,
                    'at' => [
                        'time' => $at->getTimestamp(),
                        'iso8601' => $at->format(DateTimeInterface::ATOM),
                        'natural' => $f->asDatetime($at, 'medium'),
                        'relative' => $f->asRelativeTime($at, $now),
                    ],
                ];
            }
        );
    }
}
