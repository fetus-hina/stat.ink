<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\ostatus;

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
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

class FeedAction extends BaseAction
{
    public $screen_name;

    public function init()
    {
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->language = 'ja-JP';
        parent::init();
    }

    public function run()
    {
        if (!$user = $this->user) {
            return $this->http404();
        }

        if (!$battleId = Yii::$app->getRequest()->get('battle')) {
            $resp = Yii::$app->getResponse();
            $resp->format = 'raw';
            $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
            $resp->data = $this->renderAtom($user);
            $resp->content = null;
            return $resp;
        }

        $battle = $user->getBattles()->andWhere(['id' => $battleId])->one();
        if (!$battle) {
            return $this->http404();
        }
        $resp = Yii::$app->getResponse();
        $resp->format = 'raw';
        $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
        $resp->data = $this->renderBattleAtom($user, $battle);
        $resp->content = null;
        return $resp;
    }

    public function getUser() : ?User
    {
        $screenName = trim((string)Yii::$app->getRequest()->get('screen_name'));
        return User::findOne(['screen_name' => $screenName]);
    }

    public function http404() : Response
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = 404;
        $resp->statusText = 'Not Found';
        $resp->data = ['error' => Yii::t('yii', 'Page not found.')];
        $resp->content = null;
        return $resp;
    }

    public function renderAtom(User $user) : string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->appendChild($doc->createElementNS('http://www.w3.org/2005/Atom', 'feed'));
        $root->appendChild($doc->createElement(
            'id',
            $this->text(
                $doc,
                $this->urlUuid(Url::to(['/ostatus/feed', 'screen_name' => $user->screen_name], true))
            )
        ));
        $root->appendChild($doc->createElement('title', $this->text($doc, $user->screen_name)));
        $root->appendChild($doc->createElement('subtitle', $this->text($doc, sprintf(
            '@%s@%s',
            $user->screen_name,
            Yii::$app->getRequest()->hostName
        ))));
        $root->appendChild($doc->createElement('updated', $this->text(
            $doc,
            $this->datetime($_SERVER['REQUEST_TIME'] ?? time())
        )));
        $root->appendChild($doc->createElement(
            'logo',
            $this->text($doc, $user->userIcon ? $user->userIcon->absUrl : $user->jdenticonPngUrl)
        ));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/user', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'self',
            'type' => 'application/atom+xml',
            'href' => Url::to(['/ostatus/feed', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'hub',
            'href' => Url::to(['/ostatus/pubsubhubbub'], true),
        ]));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'salmon',
            'href' => Url::to(['/ostatus/salmon', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild($this->createAuthor($doc, $user));

        $query = $user->getBattles()
            ->with(['rule', 'map'])
            ->orderBy('[[id]] DESC')
            ->limit(50);
        foreach ($query->all() as $battle) {
            $root->appendChild($this->createEntry($doc, $user, $battle));
        }

        $doc->formatOutput = !!YII_DEBUG;
        return $doc->saveXML();
    }

    public function renderBattleAtom(User $user, Battle $battle) : string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->appendChild($this->createEntry($doc, $user, $battle));
        $doc->formatOutput = !!YII_DEBUG;
        return $doc->saveXML();
    }

    private function text(DOMDocument $doc, string $text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    private function urlUuid(string $url) : string
    {
        return Uuid::v5(UuidNS::url(), $url)->formatAsUri();
    }

    private function datetime(int $time) : string
    {
        return (new DateTimeImmutable("@{$time}"))
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->format(DateTime::ATOM);
    }

    private function createElement(DOMDocument $doc, string $name, array $attributes = []) : DOMElement
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
            $this->text(
                $doc,
                $this->urlUuid(Url::to(['/show/user', 'screen_name' => $user->screen_name], true))
            )
        ));
        $root->appendChild($doc->createElementNS(
            'http://activitystrea.ms/spec/1.0/',
            'activity:object-type',
            'http://activitystrea.ms/schema/1.0/person'
        ));
        $root->appendChild($doc->createElement(
            'uri',
            $this->text(
                $doc,
                Url::to(['/show/user', 'screen_name' => $user->screen_name], true)
            )
        ));
        $root->appendChild($doc->createElement('name', $this->text($doc, $user->name)));
        $root->appendChild($doc->createElement('email', $this->text($doc, sprintf(
            '%s@%s',
            $user->screen_name,
            Yii::$app->getRequest()->hostName
        ))));
        $root->appendChild($doc->createElement('summary', ' '));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/user', 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild((function () use ($doc, $user) {
            $link = $this->createElement($doc, 'link', [
                'rel' => 'avatar',
                'type' => '',
                'href' => $user->userIcon ? $user->userIcon->absUrl : $user->jdenticonPngUrl,
            ]);
            $link->setAttributeNS('http://purl.org/syndication/atommedia', 'media:width', '120');
            $link->setAttribute('media:height', '120');
            return $link;
        })());
        $root->appendChild((function () use ($doc) {
            $link = $this->createElement($doc, 'link', [
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
            $this->text($doc, $user->screen_name)
        ));
        $root->appendChild($doc->createElementNS(
            'http://portablecontacts.net/spec/1.0',
            'poco:displayName',
            $this->text($doc, $user->name)
        ));
        $root->appendChild($doc->createElementNS(
            'http://mastodon.social/schema/1.0',
            'mastodon:scope',
            $this->text($doc, 'unlisted')
        ));
        return $root;
    }

    public function createEntry(DOMDocument $doc, User $user, Battle $battle) : DOMElement
    {
        $root = $doc->createElementNS('http://www.w3.org/2005/Atom', 'entry');
        $root->appendChild($doc->createElement(
            'id',
            Url::to(['/show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true)
        ));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => Url::to(['/show/battle', 'battle' => $battle->id, 'screen_name' => $user->screen_name], true),
        ]));
        $root->appendChild($this->createElement($doc, 'link', [
            'rel' => 'self',
            'type' => 'application/atom+xml',
            'href' => Url::to(
                ['/ostatus/battle-atom', 'battle' => $battle->id, 'screen_name' => $user->screen_name],
                true
            ),
        ]));
        $root->appendChild($doc->createElement('published', $this->datetime(strtotime($battle->at))));
        $root->appendChild($doc->createElement('updated', $this->datetime(strtotime($battle->at))));
        $root->appendChild($doc->createElement('title', sprintf('New Battle by %s', $user->screen_name)));
        $root->appendChild($doc->createElementNS(
            'http://activitystrea.ms/spec/1.0/',
            'activity:object-type',
            'http://activitystrea.ms/schema/1.0/note'
        ));
        $root->appendChild($doc->createElement('activity:verb', 'http://activitystrea.ms/schema/1.0/post'));
        $content = $root->appendChild($doc->createElement(
            'content',
            $this->text($doc, $this->createBattleHtml($user, $battle))
        ));
        $content->setAttribute('type', 'html');
        $content->setAttribute('xml:lang', 'ja-JP');
        $root->appendChild($doc->createElementNS(
            'http://mastodon.social/schema/1.0',
            'mastodon:scope',
            $this->text($doc, 'unlisted')
        ));
        if ($battle->battleImageResult) {
            $root->appendChild($this->createElement($doc, 'link', [
                'rel'    => 'enclosure',
                'type'   => 'image/jpeg',
                'length' => '0',
                'href'   => $battle->battleImageResult->url,
            ]));
        }
        if ($battle->battleImageJudge) {
            $root->appendChild($this->createElement($doc, 'link', [
                'rel'    => 'enclosure',
                'type'   => 'image/jpeg',
                'length' => '0',
                'href'   => $battle->battleImageJudge->url,
            ]));
        }
        $root->appendChild($this->createAuthor($doc, $user));
        return $root;
    }

    private function createBattleHtml(User $user, Battle $battle) : string
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
