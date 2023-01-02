<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Exception;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Validator\Uri as UriValidator;
use Yii;
use app\models\BlogEntry;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\console\Controller;

use function printf;
use function usort;

class BlogFeedController extends Controller
{
    public function actionCrawl()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $entries = $this->fetchFeed();
        foreach ($entries as $entry) {
            $this->processEntry($entry);
        }
        $transaction->commit();
    }

    private function fetchFeed()
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
        usort($ret, fn ($a, $b) => $a->getDateCreated()->getTimestamp() <=> $b->getDateCreated()->getTimestamp());
        return $ret;
    }

    private function processEntry($entry)
    {
        $id = $entry->getId() ?? $entry->getLink() ?? false;
        if (!$id) {
            return;
        }
        $uuid = Uuid::v5(
            (new UriValidator())->isValid($id)
                ? UuidNs::url()
                : 'd0ec81fc-c8e6-11e5-a890-9ca3ba01e1f8',
            $id,
        )->__toString();
        $link = $entry->getLink();
        if (!(new UriValidator())->isValid($link)) {
            return;
        }

        if (BlogEntry::find()->andWhere(['uuid' => $uuid])->count()) {
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
}
