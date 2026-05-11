<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\feed;

use DateTimeImmutable;
use DateTimeZone;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Laminas\Feed\Writer\Version;
use Override;
use Yii;
use app\models\Battle3;
use app\models\Language;
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
use function rawurlencode;
use function sprintf;
use function strtotime;
use function substr;
use function time;
use function vsprintf;

use const SORT_DESC;

final class User3Action extends Action
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
        $model = DynamicModel::validateData(
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
        if ($model->hasErrors()) {
            $resp->format = 'json';
            $resp->statusCode = 404;
            $resp->statusText = 'Not Found';
            return ['error' => $model->getErrors()];
        }

        $resp->format = 'raw';

        if (!$user = User::findOne(['screen_name' => $model->screen_name])) {
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
                Version::VERSION,
            ),
            Yii::$app->version,
            Url::home(true),
        );
        $feed->setTitle(
            Yii::t(
                'app',
                '{name}\'s Splat Log',
                ['name' => $user->name],
                $model->lang,
            ),
        );
        $feed->setDescription(
            Yii::t(
                'app',
                '{name}\'s Splat Log',
                ['name' => $user->name],
                $model->lang,
            ),
        );
        $feed->setId(
            Uuid::v5(
                UuidNS::url(),
                Url::to(['show-v3/user', 'screen_name' => $user->screen_name], true),
            )->formatAsUri(),
        );
        $feed->setLink(
            Url::to(['show-v3/user', 'screen_name' => $user->screen_name], true),
        );
        // 複数の言語を持たすことはできなさげ
        foreach (['atom', 'rss'] as $type) {
            $feed->setFeedLink(
                Url::to(
                    ['feed/user-v3',
                        'lang' => $model->lang,
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
                'uri' => Url::to(['show-v3/user', 'screen_name' => $user->screen_name], true),
            ],
        ]);
        $feed->setCopyright(
            sprintf('Copyright (C) 2015-%s AIZAWA Hina', $now->format('Y')),
        );
        $feed->setLanguage($model->lang);
        $feed->setEncoding('UTF-8');
        $feed->setDateModified((int)$now->getTimestamp());
        $feed->setLastBuildDate((int)$now->getTimestamp());
        $feed->setBaseUrl(Url::home(true));

        //FIXME
        $feed->addCategories(
            $model->lang === 'ja-JP'
                ? [
                    ['term' => 'ゲーム'],
                    ['term' => 'スプラトゥーン'],
                    ['term' => 'イカログ'],
                ]
                : [
                    ['term' => 'Game'],
                    ['term' => 'Splatoon'],
                    ['term' => 'Splatlog'],
                ],
        );

        $battles = Battle3::find()
            ->with([
                'battleImageJudge3',
                'battleImageResult3',
                'lobby',
                'map',
                'rule',
                'weapon',
                'rankBefore',
                'rankAfter',
                'result',
            ])
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->orderBy(['start_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(50)
            ->all();

        foreach ($battles as $battle) {
            $entry = $feed->createEntry();
            $entry->addAuthor([
                'name' => $user->name,
                'uri' => Url::to(
                    ['show-v3/user',
                        'screen_name' => $user->screen_name,
                    ],
                    true,
                ),
            ]);
            $entry->setDateCreated(strtotime($battle->start_at ?? $battle->created_at));
            $entry->setDateModified(strtotime($battle->updated_at));
            $entry->setId(
                Uuid::v5(
                    UuidNS::url(),
                    Url::to(
                        ['show-v3/battle',
                            'screen_name' => $user->screen_name,
                            'battle' => $battle->uuid,
                        ],
                        true,
                    ),
                )->formatAsUri(),
            );
            $entry->setLink(
                Url::to(
                    [
                        'show-v3/battle',
                        'screen_name' => $user->screen_name,
                        'battle' => $battle->uuid,
                    ],
                    true,
                ),
            );
            $entry->setTitle(
                vsprintf('%s / %s / %s - %s (#%s)', [
                    $battle->rule
                        ? Yii::t('app-rule3', $battle->rule->name, [], $model->lang)
                        : '???',
                    $battle->map
                        ? Yii::t('app-map3', $battle->map->name, [], $model->lang)
                        : '???',
                    $battle->result?->is_win === null
                        ? '???'
                        : Yii::t('app', $battle->result->is_win ? 'Won' : 'Lost', [], $model->lang),
                    $user->name,
                    substr($battle->uuid, 0, 8),
                ]),
            );
            $entry->setContent($this->makeEntryContent($battle, $model->lang));
            $feed->addEntry($entry);
        }

        $contentType = [
            'atom' => 'application/atom+xml; charset=UTF-8',
            'rss' => 'application/rss+xml; charset=UTF-8',
        ];
        $resp->format = 'raw';
        $resp->headers->set(
            'Content-Type',
            $contentType[$model->type] ?? 'text/xml; charset=UTF-8',
        );
        return $feed->export($model->type);
    }

    protected function makeEntryContent(Battle3 $battle, string $lang): string
    {
        $html = '';
        $imageUrls = $this->getEntryImageUrls($battle, $lang);
        if ($imageUrls) {
            $html .= Html::tag('p', implode('', array_map(
                fn (string $url): string => sprintf('<img src="%s" />', Html::encode($url)),
                $imageUrls,
            )));
        }

        $dl = [];
        $_ = function ($label, $value) use (&$dl) {
            $dl[] = Html::tag('dt', Html::encode($label));
            $dl[] = Html::tag('dd', Html::encode($value));
        };
        $__ = function ($label, $value, $category) use ($_, $lang) {
            $_(
                Yii::t('app', $label, [], $lang),
                Yii::t($category, $value, [], $lang),
            );
        };

        if ($battle->lobby) {
            $__('Lobby', $battle->lobby->name, 'app-lobby3');
        }
        if ($battle->rule) {
            $__('Mode', $battle->rule->name, 'app-rule3');
        }
        if ($battle->map) {
            $__('Stage', $battle->map->name, 'app-map3');
        }
        if ($battle->weapon) {
            $__('Weapon', $battle->weapon->name, 'app-weapon3');
        }
        if ($battle->rankBefore || $battle->rankAfter) {
            $_(
                Yii::t('app', 'Rank', [], $lang),
                sprintf(
                    '%s → %s',
                    $battle->rankBefore
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank3', $battle->rankBefore->name, [], $lang),
                            $battle->rank_before_exp ?? '',
                        )
                        : '???',
                    $battle->rankAfter
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank3', $battle->rankAfter->name, [], $lang),
                            $battle->rank_after_exp ?? '',
                        )
                        : '???',
                ),
            );
        }
        if ($battle->level_before !== null || $battle->level_after !== null) {
            $_(
                Yii::t('app', 'Level', [], $lang),
                sprintf(
                    '%s → %s',
                    $battle->level_before ?? '???',
                    $battle->level_after ?? '???',
                ),
            );
        }
        if ($battle->result) {
            $__('Result', $battle->result->is_win ? 'Won' : 'Lost', 'app');
            if ($battle->is_knockout !== null) {
                $dl[] = Html::tag('dt', Html::encode(
                    Yii::t('app', $battle->is_knockout ? 'Knockout' : 'Time is up', [], $lang),
                ));
            }
        }
        if ($battle->kill !== null && $battle->death !== null) {
            $_(
                Yii::t('app', 'Kills / Deaths', [], $lang),
                sprintf('%d / %d', $battle->kill, $battle->death),
            );
        }
        if ($battle->assist !== null) {
            $_(Yii::t('app', 'Assist', [], $lang), (string)$battle->assist);
        }
        if ($battle->special !== null) {
            $_(Yii::t('app', 'Specials', [], $lang), (string)$battle->special);
        }
        if ($battle->inked !== null) {
            $_(Yii::t('app', 'Inked', [], $lang), sprintf('%dp', $battle->inked));
        }
        if ($dl) {
            $html .= Html::tag('dl', implode('', $dl));
        }
        return $html ?: '<p>Splatlog</p>';
    }

    /**
     * @return list<string>
     */
    private function getEntryImageUrls(Battle3 $battle, string $lang): array
    {
        $resultImage = $battle->battleImageResult3;
        $judgeImage = $battle->battleImageJudge3;
        if ($resultImage || $judgeImage) {
            $urls = [];
            if ($resultImage) {
                $urls[] = Url::to('@imageurl/' . $resultImage->filename, true);
            }
            if ($judgeImage) {
                $urls[] = Url::to('@imageurl/' . $judgeImage->filename, true);
            }
            return $urls;
        }

        if (ArrayHelper::getValue(Yii::$app->params, 'useS3ImgGen')) {
            return [
                vsprintf('https://s3-img-gen.stats.ink/results/%s/%s.jpg', [
                    rawurlencode($lang),
                    rawurlencode($battle->uuid),
                ]),
            ];
        }

        return [];
    }
}
