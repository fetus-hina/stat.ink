<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\feed;

use DateTimeImmutable;
use DateTimeZone;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Yii;
use app\models\Battle2;
use app\models\Language;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;

class User2Action extends BaseAction
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
        $model = DynamicModel::validateData(
            [
                'lang'          => $request->get('lang'),
                'screen_name'   => $request->get('screen_name'),
                'type'          => $request->get('type'),
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
                \Laminas\Feed\Writer\Version::VERSION,
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
                Url::to(['show-v2/user', 'screen_name' => $user->screen_name], true),
            )->formatAsUri(),
        );
        $feed->setLink(
            Url::to(['show-v2/user', 'screen_name' => $user->screen_name], true),
        );
        // 複数の言語を持たすことはできなさげ
        foreach (['atom', 'rss'] as $type) {
            $feed->setFeedLink(
                Url::to(
                    ['feed/user-v2',
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
                'name'  => Yii::$app->name,
                'uri'   => Url::home(true),
            ],
            [
                'name'  => $user->name,
                'uri'   => Url::to(['show-v2/user', 'screen_name' => $user->screen_name], true),
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

        $battles = Battle2::find()
            ->with([
                'battleImageJudge',
                'battleImageResult',
                'lobby',
                'map',
                'rule',
                'weapon',
                'rank',
                'rankAfter',
                'mode',
                'version',
            ])
            ->andWhere(['user_id' => $user->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(50)
            ->all();

        foreach ($battles as $battle) {
            $entry = $feed->createEntry();
            $entry->addAuthor([
                'name' => $user->name,
                'uri' => Url::to(
                    ['show-v2/user',
                        'screen_name' => $user->screen_name,
                    ],
                    true,
                ),
            ]);
            $entry->setDateCreated(strtotime($battle->created_at));
            $entry->setDateModified(strtotime($battle->updated_at));
            $entry->setId(
                Uuid::v5(
                    UuidNS::url(),
                    Url::to(
                        ['show-v2/battle',
                            'screen_name' => $user->screen_name,
                            'battle' => $battle->id,
                        ],
                        true,
                    ),
                )->formatAsUri(),
            );
            $entry->setLink(
                Url::to(
                    [
                        'show-v2/battle',
                        'screen_name' => $user->screen_name,
                        'battle' => $battle->id,
                    ],
                    true,
                ),
            );
            $entry->setTitle(
                vsprintf('%s / %s / %s - %s (#%d)', [
                    $battle->rule
                        ? Yii::t('app-rule2', $battle->rule->name, [], $model->lang)
                        : '???',
                    $battle->map
                        ? Yii::t('app-map2', $battle->map->name, [], $model->lang)
                        : '???',
                    $battle->is_win !== null
                        ? Yii::t('app', $battle->is_win ? 'Won' : 'Lost', [], $model->lang)
                        : '???',
                    $user->name,
                    $battle->id,
                ]),
            );
            $entry->setContent($this->makeEntryContent($battle, $model->lang));
            $feed->addEntry($entry);
        }

        $contentType = [
            'atom'  => 'application/atom+xml; charset=UTF-8',
            'rss' => 'application/rss+xml; charset=UTF-8',
        ];
        $resp->format = 'raw';
        $resp->headers->set(
            'Content-Type',
            $contentType[$model->type] ?? 'text/xml; charset=UTF-8',
        );
        return $feed->export($model->type);
    }

    protected function makeEntryContent(Battle2 $battle, $lang)
    {
        $html = '';
        if ($battle->battleImageResult || $battle->battleImageJudge) {
            $html .= Html::tag('p', implode('', [
                $battle->battleImageResult
                    ? sprintf('<img src="%s" />', Html::encode($battle->battleImageResult->url))
                    : '',
                $battle->battleImageJudge
                    ? sprintf('<img src="%s" />', Html::encode($battle->battleImageJudge->url))
                    : '',
            ]));
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
            $_(
                Yii::t('app', 'Lobby', [], $lang),
                (function () use ($battle, $lang) {
                    // {{{
                    switch ($battle->mode->key ?? '') {
                        default:
                            return '?';

                        case 'regular':
                            return Yii::t('app-rule2', $battle->mode->name, [], $lang);

                        case 'fest':
                            switch ($battle->lobby->key ?? '') {
                                case 'fest_normal':
                                    return Yii::t('app-rule2', 'Splatfest (Normal)', [], $lang);

                                case 'standard':
                                    if ($battle->version) {
                                        if (version_compare($battle->version->tag, '4.0.0', '<')) {
                                            return Yii::t('app-rule2', 'Splatfest (Solo)', [], $lang);
                                        } else {
                                            return Yii::t('app-rule2', 'Splatfest (Pro)', [], $lang);
                                        }
                                    }
                                    return Yii::t('app-rule2', 'Splatfest (Pro/Solo)', [], $lang);

                                case 'squad_4':
                                    return Yii::t('app-rule2', 'Splatfest (Team)', [], $lang);
                            }
                            return Yii::t('app-rule2', 'Splatfest', [], $lang);

                        case 'gachi':
                            switch ($battle->lobby->key ?? '') {
                                case 'standard':
                                    return Yii::t('app-rule2', 'Ranked Battle (Solo)', [], $lang);

                                case 'squad_2':
                                    return Yii::t('app-rule2', 'League Battle (Twin)', [], $lang);

                                case 'squad_4':
                                    return Yii::t('app-rule2', 'League Battle (Quad)', [], $lang);
                            }
                            return Yii::t('app-rule2', 'Ranked Battle', [], $lang);

                        case 'private':
                            return Yii::t('app-rule2', 'Private Battle', [], $lang);
                    }
                    // }}}
                })(),
            );
        }
        if ($battle->rule) {
            $__('Mode', $battle->rule->name, 'app-rule2');
        }
        if ($battle->map) {
            $__('Stage', $battle->map->name, 'app-map2');
        }
        if ($battle->weapon) {
            $__('Weapon', $battle->weapon->name, 'app-weapon2');
        }
        if ($battle->rank || $battle->rankAfter) {
            $_(
                Yii::t('app', 'Rank', [], $lang),
                sprintf(
                    '%s → %s',
                    $battle->rank
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank2', $battle->rank->name, [], $lang),
                            $battle->rank_exp ?? '',
                        )
                        : '???',
                    $battle->rankAfter
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank2', $battle->rankAfter->name, [], $lang),
                            $battle->rank_after_exp ?? '',
                        )
                        : '???',
                ),
            );
        }
        if ($battle->level) {
            $_(Yii::t('app', 'Level', [], $lang), $battle->level);
        }
        if ($battle->is_win !== null) {
            $__('Result', $battle->is_win ? 'Won' : 'Lost', 'app');
            if ($battle->isGachi && $battle->is_knockout !== null) {
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
            $_(
                Yii::t('app', 'Kill Ratio', [], $lang),
                $battle->kill_ratio === null
                    ? Yii::t('app', 'N/A', [], $lang)
                    : sprintf('%.2f', $battle->kill_ratio),
            );
        }
        if (!empty($dl)) {
            $html .= Html::tag('dl', implode('', $dl));
        }
        return empty($html) ? '<p>Splatlog</p>' : $html;
    }
}
