<?php

/**
 * @copyright Copyright (C) 2020-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon;

use DateTimeImmutable;
use DateTimeZone;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Laminas\Feed\Writer\Version as FeedVersion;
use Yii;
use app\components\i18n\Formatter;
use app\models\Language;
use app\models\Salmon2;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use function array_map;
use function implode;
use function sprintf;
use function strtotime;
use function time;
use function vsprintf;

use const SORT_DESC;

class FeedAction extends Action
{
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

        $now = (new DateTimeImmutable())
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
                Url::to(['salmon/index', 'screen_name' => $user->screen_name], true),
            )->formatAsUri(),
        );
        $feed->setLink(Url::to(['salmon/index', 'screen_name' => $user->screen_name], true));
        // 複数の言語を持たすことはできなさげ
        foreach (['atom', 'rss'] as $type) {
            $feed->setFeedLink(
                Url::to(
                    ['salmon/feed',
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
                'uri' => Url::to(['salmon/index', 'screen_name' => $user->screen_name], true),
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

        $models = Salmon2::find()
            ->orderBy(['id' => SORT_DESC])
            ->andWhere(['user_id' => $user->id])
            ->with([
                'failReason',
                'players',
                'players.special',
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
                'uri' => Url::to(['salmon/index', 'screen_name' => $user->screen_name], true),
            ]);
            $entry->setDateCreated(strtotime($model->created_at));
            $entry->setDateModified(strtotime($model->updated_at));
            $entry->setId(
                Uuid::v5(
                    UuidNS::url(),
                    Url::to(
                        ['salmon/view',
                            'screen_name' => $user->screen_name,
                            'id' => $model->id,
                        ],
                        true,
                    ),
                )->formatAsUri(),
            );
            $entry->setLink(
                Url::to(
                    [
                        'salmon/view',
                        'screen_name' => $user->screen_name,
                        'id' => $model->id,
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

    protected function makeEntryTitle(Salmon2 $model, string $lang): string
    {
        $result = [];
        if ($model->clear_waves !== null) {
            if ($model->clear_waves == 3) {
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

        return vsprintf('%s - %s (#%d)', [
            implode(' / ', ArrayHelper::toFlatten([
                Yii::t('app-salmon2', 'Salmon Run', [], $lang),
                Yii::t('app-salmon-map2', $model->stage->name ?? '?', [], $lang),
                $result ?: '?',
            ])),
            $model->user->name,
            $model->id,
        ]);
    }

    protected function makeEntryContent(Salmon2 $model, string $lang): string
    {
        $fmt = Yii::createObject([
            '__class' => Formatter::class,
            'language' => $lang,
            'locale' => $lang,
            'nullDisplay' => '?',
        ]);

        $data = [
            [
                Yii::t('app', 'Stage', [], $lang),
                Yii::t('app-salmon-map2', $model->stage->name ?? '?', [], $lang),
            ],
            [
                Yii::t('app-salmon2', 'Hazard Level', [], $lang),
                $model->danger_rate === null
                    ? '?'
                    : vsprintf('%s (%s: %s)', [
                        $fmt->asDecimal($model->danger_rate, 1),
                        Yii::t('app-salmon2', 'Quota', [], $lang),
                        implode(' - ', $model->quota ?? []),
                    ]),
            ],
            [
                Yii::t('app', 'Special', [], $lang),
                Yii::t('app-special2', $model->myData->special->name ?? null, [], $lang),
            ],
            [
                Yii::t('app-salmon2', 'Deaths', [], $lang),
                $fmt->asInteger($model->myData->death ?? null),
            ],
            [
                Yii::t('app-salmon2', 'Rescues', [], $lang),
                $fmt->asInteger($model->myData->rescue ?? null),
            ],
        ];

        return Html::tag('dl', implode('', array_map(fn (array $row): string => Html::tag('dt', Html::encode($row[0])) . Html::tag('dd', Html::encode($row[1])), $data)));
    }
}
