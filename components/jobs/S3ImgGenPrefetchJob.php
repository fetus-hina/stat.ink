<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\models\Battle3;
use app\models\Salmon3;
use yii\base\BaseObject;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;
use yii\queue\JobInterface;
use yii\queue\Queue;

use function rawurlencode;
use function vsprintf;

final class S3ImgGenPrefetchJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public const TYPE_BATTLE = 'battle';
    public const TYPE_SALMON = 'salmon';

    public string $uuid = '00000000-0000-0000-0000-000000000000';

    /**
     * @var TYPE_* $type
     */
    public string $type = self::TYPE_BATTLE;

    /**
     * @inheritdoc
     * @param Queue $queue
     */
    public function execute($queue)
    {
        $item = $this->findItem($this->type, $this->uuid);
        if (!$item) {
            return;
        }

        $locales = [
            'ja-JP',

            'en-US',
            'en-GB',

            'de-DE',
            'fr-FR',
            'ko-KR',
            'pt-BR',
            'zh-CN',
            'zh-TW',
        ];

        foreach ($locales as $locale) {
            $url = $this->generateUrl($item, $locale);
            if ($this->prefetch($url)) {
                echo "Prefetched: {$url}\n";
            } else {
                echo "Failed to prefetch: {$url}\n";
            }
        }
    }

    /**
     * @param self::TYPE_* $type
     */
    private function findItem(string $type, string $uuid): Battle3|Salmon3|null
    {
        return match ($type) {
            self::TYPE_BATTLE => Battle3::find()
                ->andWhere([
                    'is_deleted' => false,
                    'uuid' => $uuid,
                ])
                ->limit(1)
                ->one(),

            self::TYPE_SALMON => Salmon3::find()
                ->andWhere([
                    'is_deleted' => false,
                    'uuid' => $uuid,
                ])
                ->limit(1)
                ->one(),

            default => null,
        };
    }

    private function generateUrl(Battle3|Salmon3 $item, string $locale): string
    {
        return vsprintf('https://s3-img-gen.stats.ink/%s/%s/%s.jpg', [
            rawurlencode(
                match ($this->type) {
                    self::TYPE_BATTLE => 'results',
                    self::TYPE_SALMON => 'salmon',
                },
            ),
            rawurlencode($locale),
            rawurlencode($item->uuid),
        ]);
    }

    private function prefetch(string $url): bool
    {
        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
        ]);
        $request = $client->createRequest()
            ->setMethod('get')
            ->setUrl($url);
        $request->headers->set('User-Agent', 'stat.ink prefetcher');
        return $request->send()->isOk;
    }
}
