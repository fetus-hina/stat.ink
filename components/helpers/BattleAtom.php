<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use DOMDocument;
use DOMElement;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Battle;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use yii\helpers\Url;

class BattleAtom
{
    public static function createUserFeed(User $user, array $only = []) : ?string
    {
        $raii = self::switchLanguage('ja-JP'); // FIXME
        return static::renderAtom($user, $only);
    }

    public static function createBattleFeed(User $user, Battle $battle) : ?string
    {
        $raii = self::switchLanguage('ja-JP'); // FIXME
        return static::renderBattleAtom($user, $battle);
    }

    private static function switchLanguage(string $lang) // : object
    {
        $oldLang = Yii::$app->language;
        $oldTZ = Yii::$app->timeZone;

        Yii::$app->language = $lang;
        Yii::$app->timeZone = 'Etc/UTC';

        return new class($oldLang, $oldTZ) {
            private $oldLang;
            private $oldTZ;

            public function __construct(string $oldLang, string $oldTZ)
            {
                $this->oldLang = $oldLang;
                $this->oldTZ = $oldTZ;
            }

            public function __destruct()
            {
                Yii::$app->language = $this->oldLang;
                Yii::$app->timeZone = $this->oldTZ;
            }
        };
    }

    protected static function renderAtom(User $user, array $only = []) : ?string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->appendChild($doc->createElementNS('http://www.w3.org/2005/Atom', 'feed'));
        $root->appendChild($doc->createElement(
            'id',
            static::text(
                $doc,
                static::urlUuid(Url::to(['/ostatus/feed', 'screen_name' => $user->screen_name], true))
            )
        ));
        $root->appendChild($doc->createElement('title', static::text($doc, $user->screen_name)));
        $root->appendChild($doc->createElement('subtitle', static::text($doc, sprintf(
            '@%s@%s',
            $user->screen_name,
            Yii::$app->getRequest()->hostName
        ))));
        $root->appendChild($doc->createElement('updated', static::text(
            $doc,
            static::datetime($_SERVER['REQUEST_TIME'] ?? time())
        )));
        $root->appendChild($doc->createElement(
            'logo',
            static::text($doc, $user->userIcon ? $user->userIcon->absUrl : $user->jdenticonPngUrl)
        ));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/user', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'self',
            'type' => 'application/atom+xml',
            'href' => Url::to(['/ostatus/feed', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'hub',
            'href' => Url::to(['/ostatus/pubsubhubbub'], true),
        ]));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'salmon',
            'href' => Url::to(['/ostatus/salmon', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild(static::createAuthor($doc, $user));

        $query = $user->getBattles()
            ->with(['rule', 'map'])
            ->orderBy('[[id]] DESC')
            ->limit(50);
        if ($only) {
            $query->andWhere(['id' => $only]);
        }
        foreach ($query->all() as $battle) {
            $root->appendChild(static::createEntry($doc, $user, $battle, false));
        }
        $doc->formatOutput = !!YII_DEBUG;
        return $doc->saveXML();
    }

    protected static function renderBattleAtom(User $user, Battle $battle) : ?string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->appendChild(static::createEntry($doc, $user, $battle));
        $doc->formatOutput = !!YII_DEBUG;
        return $doc->saveXML();
    }

    private static function text(DOMDocument $doc, string $text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    private static function urlUuid(string $url) : string
    {
        return Uuid::v5(UuidNS::url(), $url)->formatAsUri();
    }

    private static function datetime(int $time) : string
    {
        return (new DateTimeImmutable("@{$time}"))
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->format(DateTime::ATOM);
    }

    private static function createElement(
        DOMDocument $doc,
        string $name,
        array $attributes = []) : DOMElement
    {
        $e = $doc->createElement($name);
        foreach ($attributes as $k => $v) {
            $e->setAttribute($k, $v);
        }
        return $e;
    }

    private function createAuthor(DOMDocument $doc, User $user) : DOMElement
    {
        $root = $doc->createElement('author');
        $root->appendChild($doc->createElement(
            'id',
            static::text(
                $doc,
                static::urlUuid(Url::to(['/show/user', 'screen_name' => $user->screen_name], true))
            )
        ));
        $root->appendChild($doc->createElementNS(
            'http://activitystrea.ms/spec/1.0/',
            'activity:object-type',
            'http://activitystrea.ms/schema/1.0/person'
        ));
        $root->appendChild($doc->createElement(
            'uri',
            static::text(
                $doc,
                Url::to(['/show/user', 'screen_name' => $user->screen_name], true)
            )
        ));
        $root->appendChild($doc->createElement('name', static::text($doc, $user->name)));
        $root->appendChild($doc->createElement('email', static::text($doc, sprintf(
            '%s@%s',
            $user->screen_name,
            Yii::$app->getRequest()->hostName
        ))));
        $root->appendChild($doc->createElement('summary', ' '));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/user', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild((function () use ($doc, $user) {
            $link = static::createElement($doc, 'link', [
                'rel' => 'avatar',
                'type' => '',
                'href' => $user->userIcon ? $user->userIcon->absUrl : $user->jdenticonPngUrl,
            ]);
            $link->setAttributeNS('http://purl.org/syndication/atommedia', 'media:width', '120');
            $link->setAttribute('media:height', '120');
            return $link;
        })());
        $root->appendChild((function () use ($doc) {
            $link = static::createElement($doc, 'link', [
                'rel' => 'header',
                'type' => '',
                'href' => '/headers/original/missing.png',
            ]);
            $link->setAttributeNS('http://purl.org/syndication/atommedia', 'media:width', '700');
            $link->setAttribute('media:height', '335');
            return $link;
        })());
        $root->appendChild($doc->createElementNS(
            'http://portablecontacts.net/spec/1.0',
            'poco:preferredUsername',
            static::text($doc, $user->screen_name)
        ));
        $root->appendChild($doc->createElementNS(
            'http://portablecontacts.net/spec/1.0',
            'poco:displayName',
            static::text($doc, $user->name)
        ));
        $root->appendChild($doc->createElementNS(
            'http://mastodon.social/schema/1.0',
            'mastodon:scope',
            static::text($doc, 'unlisted')
        ));
        return $root;
    }

    protected static function createEntry(
        DOMDocument $doc,
        User $user,
        Battle $battle,
        bool $includeUser = true) : DOMElement
    {
        $root = $doc->createElementNS('http://www.w3.org/2005/Atom', 'entry');
        $root->appendChild($doc->createElement(
            'id',
            Url::to(['/show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true)
        ));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/battle', 'battle' => $battle->id, 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'self',
            'type' => 'application/atom+xml',
            'href' => Url::to(
                ['/ostatus/battle-atom', 'battle' => $battle->id, 'screen_name' => $user->screen_name],
                true
            ),
        ]));
        $root->appendChild(static::createElement($doc, 'link', [
            'rel' => 'mentioned',
            'xmlns:ostatus' => 'http://ostatus.org/schema/1.0',
            'ostatus:object-type' => 'http://activitystrea.ms/schema/1.0/collection',
            'href' => 'http://activityschema.org/collection/public',
        ]));
        $root->appendChild($doc->createElement('published', static::datetime(strtotime($battle->at))));
        $root->appendChild($doc->createElement('updated', static::datetime(strtotime($battle->at))));
        $root->appendChild($doc->createElement('title', sprintf('New Battle by %s', $user->screen_name)));
        $root->appendChild($doc->createElementNS(
            'http://activitystrea.ms/spec/1.0/',
            'activity:object-type',
            'http://activitystrea.ms/schema/1.0/note'
        ));
        $root->appendChild($doc->createElementNS(
            'http://activitystrea.ms/spec/1.0/',
            'activity:verb',
            'http://activitystrea.ms/schema/1.0/post'
        ));
        $content = $root->appendChild($doc->createElement(
            'content',
            static::text($doc, static::createBattleHtml($user, $battle))
        ));
        $content->setAttribute('type', 'html');
        $content->setAttribute('xml:lang', 'ja-JP');
        $root->appendChild($doc->createElementNS(
            'http://mastodon.social/schema/1.0',
            'mastodon:scope',
            static::text($doc, 'unlisted')
        ));
        if ($battle->battleImageResult) {
            $root->appendChild(static::createElement($doc, 'link', [
                'rel'    => 'enclosure',
                'type'   => 'image/jpeg',
                'length' => '0',
                'href'   => $battle->battleImageResult->url,
            ]));
        }
        if ($battle->battleImageJudge) {
            $root->appendChild(static::createElement($doc, 'link', [
                'rel'    => 'enclosure',
                'type'   => 'image/jpeg',
                'length' => '0',
                'href'   => $battle->battleImageJudge->url,
            ]));
        }
        if ($includeUser) {
            $root->appendChild(static::createAuthor($doc, $user));
        }
        return $root;
    }

    private static function createBattleHtml(User $user, Battle $battle) : string
    {
        $text = sprintf(
            '%sでの%sで%s。(%sk/%sd)',
            $battle->map
                ? Yii::t('app-map', $battle->map->name)
                : Yii::t('app', 'Splatoon'),
            $battle->rule
                ? Yii::t('app-rule', $battle->rule->name)
                : Yii::t('app', 'battle'),
            $battle->is_win === null
                ? '戦いました'
                : ($battle->is_win ? '勝ちました' : '負けました'),
            $battle->kill === null ? '??' : $battle->kill,
            $battle->death === null ? '??' : $battle->death
        );
        return sprintf('<p>%s</p>', implode(' ', [
            htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
            sprintf(
                '<a href="%s">%s</a>',
                Url::to(['/show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true),
                Yii::$app->getRequest()->hostName
            )
        ]));
    }
}
