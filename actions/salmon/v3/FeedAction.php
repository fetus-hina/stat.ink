<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\salmon\v3;

use DateTimeImmutable;
use DateTimeZone;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Laminas\Feed\Writer\Version as FeedVersion;
use Override;
use Yii;
use app\components\i18n\Formatter;
use app\models\Language;
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use function array_filter;
use function array_map;
use function array_values;
use function implode;
use function rawurlencode;
use function sprintf;
use function strtotime;
use function substr;
use function time;
use function vsprintf;

use const SORT_DESC;

final class FeedAction extends Action
{
    #[Override]
    public function init()
    {
        parent::init();

        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->language = 'en-US';
    }

    public function run()
    {
        $request = Yii::$app->getRequest();
        $resp = Yii::$app->getResponse();
        $input = DynamicModel::validateData(
            [
                'lang' => $request->get('lang'),
                'screen_name' => $request->get('screen_name'),
                'type' => $request->get('type'),
            ],
            [
                [['lang', 'screen_name', 'type'], 'required'],
                [['lang'], 'exist',
                    'targetClass' => Language::class,
                    'targetAttribute' => 'lang',
                ],
                [['screen_name'], 'exist',
                    'targetClass' => User::class,
                    'targetAttribute' => 'screen_name',
                ],
                [['type'], 'in', 'range' => ['atom', 'rss']],
            ],
        );
        if ($input->hasErrors()) {
            $resp->format = 'json';
            $resp->statusCode = 404;
            $resp->statusText = 'Not Found';
            return ['error' => $input->getErrors()];
        }

        $resp->format = 'raw';
        if (!$user = User::findOne(['screen_name' => $input->screen_name])) {
            $resp->format = 'json';
            $resp->statusCode = 500;
            $resp->statusText = 'Internal Server Error';
            return ['error' => 'something happend'];
        }

        $now = new DateTimeImmutable()
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()));

        $feed = new FeedWriter();
        $feed->setGenerator(
            sprintf(
                '%s/%s %s/%s',
                Yii::$app->name,
                Yii::$app->version,
                'Laminas-Feed-Writer',
                FeedVersion::VERSION,
            ),
            Yii::$app->version,
            Url::home(true),
        );
        $feed->setTitle(
            Yii::t('app', '{name}\'s Salmon Log', ['name' => $user->name], $input->lang),
        );
        $feed->setDescription(
            Yii::t('app', '{name}\'s Salmon Log', ['name' => $user->name], $input->lang),
        );
        $feed->setId(
            Uuid::v5(
                UuidNS::url(),
                Url::to(['salmon-v3/index', 'screen_name' => $user->screen_name], true),
            )->formatAsUri(),
        );
        $feed->setLink(Url::to(['salmon-v3/index', 'screen_name' => $user->screen_name], true));
        // 複数の言語を持たすことはできなさげ
        foreach (['atom', 'rss'] as $type) {
            $feed->setFeedLink(
                Url::to(
                    ['salmon-v3/feed',
                        'lang' => $input->lang,
                        'screen_name' => $user->screen_name,
                        'type' => $type,
                    ],
                    true,
                ),
                $type,
            );
        }
        $feed->addAuthors([
            [
                'name' => Yii::$app->name,
                'uri' => Url::home(true),
            ],
            [
                'name' => $user->name,
                'uri' => Url::to(['salmon-v3/index', 'screen_name' => $user->screen_name], true),
            ],
        ]);
        $feed->setCopyright(
            sprintf('Copyright (C) 2015-%s AIZAWA Hina', $now->format('Y')),
        );
        $feed->setLanguage($input->lang);
        $feed->setEncoding('UTF-8');
        $feed->setDateModified((int)$now->getTimestamp());
        $feed->setLastBuildDate((int)$now->getTimestamp());
        $feed->setBaseUrl(Url::home(true));

        //FIXME
        $feed->addCategories(
            $input->lang === 'ja-JP'
                ? [
                    ['term' => 'ゲーム'],
                    ['term' => 'スプラトゥーン'],
                    ['term' => 'サーモンラン'],
                ]
                : [
                    ['term' => 'Game'],
                    ['term' => 'Splatoon'],
                    ['term' => 'Salmon Run'],
                ],
        );

        $models = Salmon3::find()
            ->orderBy(['start_at' => SORT_DESC, 'id' => SORT_DESC])
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->with([
                'bigStage',
                'failReason',
                'salmonPlayer3s',
                'salmonPlayer3s.special',
                'stage',
                'titleAfter',
                'titleBefore',
                'user',
            ])
            ->limit(50)
            ->all();
        foreach ($models as $model) {
            $entry = $feed->createEntry();
            $entry->addAuthor([
                'name' => $user->name,
                'uri' => Url::to(['salmon-v3/index', 'screen_name' => $user->screen_name], true),
            ]);
            $entry->setDateCreated(strtotime($model->start_at ?? $model->created_at));
            $entry->setDateModified(strtotime($model->updated_at));
            $entry->setId(
                Uuid::v5(
                    UuidNS::url(),
                    Url::to(
                        ['salmon-v3/view',
                            'screen_name' => $user->screen_name,
                            'battle' => $model->uuid,
                        ],
                        true,
                    ),
                )->formatAsUri(),
            );
            $entry->setLink(
                Url::to(
                    [
                        'salmon-v3/view',
                        'screen_name' => $user->screen_name,
                        'battle' => $model->uuid,
                    ],
                    true,
                ),
            );
            $entry->setTitle($this->makeEntryTitle($model, $input->lang));
            $entry->setContent($this->makeEntryContent($model, $input->lang));
            $feed->addEntry($entry);
        }

        $contentType = [
            'atom' => 'application/atom+xml; charset=UTF-8',
            'rss' => 'application/rss+xml; charset=UTF-8',
        ];
        $resp->format = 'raw';
        $resp->headers->set(
            'Content-Type',
            $contentType[$input->type] ?? 'text/xml; charset=UTF-8',
        );
        return $feed->export($input->type);
    }

    private function makeEntryTitle(Salmon3 $model, string $lang): string
    {
        $result = [];
        if ($model->clear_waves !== null) {
            $cleared = $model->is_eggstra_work
                ? $model->clear_waves >= 5
                : $model->clear_waves >= 3;
            if ($cleared) {
                $result[] = Yii::t('app-salmon2', 'Cleared', [], $lang);
            } else {
                $result[] = Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                    'waveNumber' => $model->clear_waves + 1,
                ], $lang);
                if ($model->failReason) {
                    $result[] = Yii::t('app-salmon2', $model->failReason->name, [], $lang);
                }
            }
        }

        $stageName = $this->getStageName($model, $lang);
        $jobLabel = $this->getJobLabel($model, $lang);

        return vsprintf('%s - %s (#%s)', [
            implode(' / ', ArrayHelper::toFlatten([
                $jobLabel,
                $stageName ?? '?',
                $result ?: '?',
            ])),
            $model->user->name,
            substr($model->uuid, 0, 8),
        ]);
    }

    private function makeEntryContent(Salmon3 $model, string $lang): string
    {
        $fmt = Yii::createObject([
            'class' => Formatter::class,
            'language' => $lang,
            'locale' => $lang,
            'nullDisplay' => '?',
        ]);

        $me = $this->getMyPlayer($model);

        $html = '';
        $imageUrl = $this->getEntryImageUrl($model, $lang);
        if ($imageUrl !== null) {
            $html .= Html::tag('p', sprintf('<img src="%s" />', Html::encode($imageUrl)));
        }

        $data = [
            [
                Yii::t('app', 'Stage', [], $lang),
                $this->getStageName($model, $lang) ?? '?',
            ],
            [
                Yii::t('app-salmon2', 'Hazard Level', [], $lang),
                $model->danger_rate === null
                    ? '?'
                    : $fmt->asDecimal((float)$model->danger_rate, 1),
            ],
            [
                Yii::t('app', 'Special', [], $lang),
                Yii::t('app-special3', $me?->special?->name ?? null, [], $lang),
            ],
            [
                Yii::t('app-salmon2', 'Golden Eggs', [], $lang),
                $fmt->asInteger($me?->golden_eggs ?? null),
            ],
            [
                Yii::t('app-salmon2', 'Power Eggs', [], $lang),
                $fmt->asInteger($me?->power_eggs ?? null),
            ],
            [
                Yii::t('app-salmon2', 'Rescues', [], $lang),
                $fmt->asInteger($me?->rescue ?? null),
            ],
            [
                Yii::t('app-salmon2', 'Deaths', [], $lang),
                $fmt->asInteger($me?->rescued ?? null),
            ],
        ];

        $html .= Html::tag('dl', implode('', array_map(
            fn (array $row): string =>
                Html::tag('dt', Html::encode($row[0])) . Html::tag('dd', Html::encode($row[1])),
            $data,
        )));

        return $html;
    }

    private function getEntryImageUrl(Salmon3 $model, string $lang): ?string
    {
        if (ArrayHelper::getValue(Yii::$app->params, 'useS3ImgGen')) {
            return vsprintf('https://s3-img-gen.stats.ink/salmon/%s/%s.jpg', [
                rawurlencode($lang),
                rawurlencode($model->uuid),
            ]);
        }

        return null;
    }

    private function getStageName(Salmon3 $model, string $lang): ?string
    {
        $stage = $model->is_big_run ? $model->bigStage : $model->stage;
        return $stage
            ? Yii::t('app-map3', $stage->name, [], $lang)
            : null;
    }

    private function getJobLabel(Salmon3 $model, string $lang): string
    {
        if ($model->is_eggstra_work) {
            return Yii::t('app-salmon3', 'Eggstra Work', [], $lang);
        }
        if ($model->is_big_run) {
            return Yii::t('app-salmon3', 'Big Run', [], $lang);
        }
        return Yii::t('app-salmon2', 'Salmon Run', [], $lang);
    }

    private function getMyPlayer(Salmon3 $model): ?SalmonPlayer3
    {
        $players = array_values(array_filter(
            $model->salmonPlayer3s,
            fn (SalmonPlayer3 $p): bool => (bool)$p->is_me,
        ));
        return $players[0] ?? null;
    }
}
