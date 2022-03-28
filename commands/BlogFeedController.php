<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Exception;
use Laminas\Feed\Reader\Entry\Rss as FeedEntry;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Validator\Uri as UriValidator;
use Yii;
use app\models\BlogEntry;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\console\Controller;
use yii\console\ExitCode;

class BlogFeedController extends Controller
{
    public function actionCrawl(): int
    {
        return Yii::$app->db->transaction(function (): int {
            $entries = $this->fetchFeed();
            foreach ($entries as $entry) {
                $this->processEntry($entry);
            }
            return ExitCode::OK;
        });
    }

    private function fetchFeed(): array
    {
        echo "Fetching feed...\n";
        $feed = FeedReader::import('https://blog.fetus.jp/category/website/stat-ink/feed');
        echo "done.\n";
        $ret = [];
        foreach ($feed as $entry) {
            if (!$entry->getDateCreated()) {
                continue;
            }
            $ret[] = $entry;
        }
        usort(
            $ret,
            fn ($a, $b) => $a->getDateCreated()->getTimestamp() <=> $b->getDateCreated()->getTimestamp(),
        );
        return $ret;
    }

    private function processEntry(FeedEntry $entry): void
    {
        if (!$id = $this->getEntryId($entry)) {
            return;
        }
        $uuid = Uuid::v5(
            (new UriValidator())->isValid($id)
                ? UuidNS::url()
                : 'd0ec81fc-c8e6-11e5-a890-9ca3ba01e1f8',
            $id
        )->__toString();
        $link = $entry->getLink();
        if (!(new UriValidator())->isValid($link)) {
            return;
        }

        if (BlogEntry::find()->andWhere(['uuid' => $uuid])->exists()) {
            return;
        }

        $model = new BlogEntry();
        $model->attributes = [
            'uuid' => $uuid,
            'url' => $link,
            'title' => $entry->getTitle(),
            'at' => $entry->getDateCreated()->format('Y-m-d\TH:i:sP'),
        ];
        if (!$model->save()) {
            echo "Could not create new blog entry\n";
            throw new Exception('Could not create new blog entry');
        }
        echo "Registered new blog entry\n";
        printf("  #%d, %s %s\n", $model->id, $model->url, $model->title);
    }

    private function getEntryId(FeedEntry $entry): ?string
    {
        if ($entry->getId()) {
            return $entry->getId();
        }

        if ($entry->getLink()) {
            return $entry->getLink();
        }

        return null;
    }
}
