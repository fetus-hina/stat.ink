<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Allen Pestaluky <allenwp@live.ca>
 */

declare(strict_types=1);

namespace app\actions\feed;

use DateTimeImmutable;
use DateTimeZone;
use Laminas\Feed\Writer\Feed as FeedWriter;
use Yii;
use app\models\Battle;
use app\models\Language;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\Url;

final class UserAction extends Action
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

        $now = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()));

        $feed = new FeedWriter();
        $feed->setGenerator(
            \vsprintf('%s/%s %s/%s', [
                Yii::$app->name,
                Yii::$app->version,
                'Laminas-Feed-Writer',
                \Laminas\Feed\Writer\Version::VERSION,
            ]),
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
                Url::to(['show-compat/user', 'screen_name' => $user->screen_name], true),
            )->formatAsUri(),
        );
        $feed->setLink(
            Url::to(['show/user', 'screen_name' => $user->screen_name], true),
        );
        // 複数の言語を持たすことはできなさげ
        foreach (['atom', 'rss'] as $type) {
            $feed->setFeedLink(
                Url::to(
                    ['feed/user',
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
                'uri'   => Url::to(['show/user', 'screen_name' => $user->screen_name], true),
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

        $battles = Battle::find()
            ->with([
                'battleImageJudge',
                'battleImageResult',
                'lobby',
                'map',
                'rule',
                'weapon',
                'rank',
                'rankAfter',
            ])
            ->andWhere(['user_id' => $user->id])
            ->limit(50)
            ->all();
        foreach ($battles as $battle) {
            $entry = $feed->createEntry();
            $entry->addAuthor([
                'name' => $user->name,
                'uri' => Url::to(['show/user', 'screen_name' => $user->screen_name], true),
            ]);
            $at = strtotime($battle->at);
            $entry->setDateCreated($at);
            $entry->setDateModified($at);
            $entry->setId(
                Uuid::v5(
                    UuidNS::url(),
                    Url::to(['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true),
                )->formatAsUri(),
            );
            $entry->setLink(
                Url::to(
                    [
                        'show/battle',
                        'screen_name' => $user->screen_name,
                        'battle' => $battle->id,
                    ],
                    true,
                ),
            );
            $entry->setTitle(
                sprintf(
                    '%s / %s / %s - %s (#%d)',
                    $battle->rule ? Yii::t('app-rule', $battle->rule->name, [], $model->lang) : '???',
                    $battle->map ? Yii::t('app-map', $battle->map->name, [], $model->lang) : '???',
                    $battle->is_win !== null
                        ? Yii::t('app', $battle->is_win ? 'Won' : 'Lost', [], $model->lang)
                        : '???',
                    $user->name,
                    $battle->id,
                ),
            );
            $entry->setContent($this->makeEntryContent($battle, $model->lang));
            $feed->addEntry($entry);
        }

        $contentType = [
            'atom'  => 'application/atom+xml; charset=UTF-8',
            'rss' => 'application/rss+xml; charset=UTF-8',
        ];
        $resp->format = 'raw';
        $resp->headers->set('Content-Type', $contentType[$model->type] ?? 'text/xml; charset=UTF-8');
        return $feed->export($model->type);
    }

    protected function makeEntryContent(Battle $battle, $lang)
    {
        $html = '';
        if ($battle->battleImageResult || $battle->battleImageJudge) {
            $html .= '<p>';
            if ($battle->battleImageResult) {
                $html .= sprintf('<img src="%s" />', Html::encode($battle->battleImageResult->url));
            }
            if ($battle->battleImageJudge) {
                $html .= sprintf('<img src="%s" />', Html::encode($battle->battleImageJudge->url));
            }
            $html .= '</p>';
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
            $__('Lobby', $battle->lobby->name, 'app-rule');
        }
        if ($battle->rule) {
            $__('Mode', $battle->rule->name, 'app-rule');
        }
        if ($battle->map) {
            $__('Stage', $battle->map->name, 'app-map');
        }
        if ($battle->weapon) {
            $__('Weapon', $battle->weapon->name, 'app-weapon');
        }
        if ($battle->rank || $battle->rankAfter) {
            $_(
                Yii::t('app', 'Rank', [], $lang),
                sprintf(
                    '%s → %s',
                    $battle->rank
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank', $battle->rank->name, [], $lang),
                            $battle->rank_exp ?? '',
                        )
                        : '???',
                    $battle->rankAfter
                        ? sprintf(
                            '%s %s',
                            Yii::t('app-rank', $battle->rankAfter->name, [], $lang),
                            $battle->rank_exp_after ?? '',
                        )
                        : '???',
                ),
            );
        }
        if ($battle->level) {
            $_(Yii::t('app', 'Level', [], $lang), $battle->level);
        }
        if ($battle->is_win !== null) {
            $__('Result', $battle->is_win ? 'WON' : 'LOST', 'app');
            if ($battle->isGachi && $battle->is_knock_out !== null) {
                $dl[] = '<dt>' . Yii::t('app', $battle->is_knock_out ? 'KNOCKOUT' : 'TIME IS UP', [], $lang) . '</dt>';
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
            $html .= '<dl>' . implode('', $dl) . '</dl>';
        }
        return empty($html) ? '<p>Splatlog</p>' : $html;
    }
}
