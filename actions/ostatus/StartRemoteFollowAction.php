<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\ostatus;

use Curl\Curl;
use DOMDocument;
use DOMXpath;
use Throwable;
use Yii;
use app\models\RemoteFollowModalForm;
use yii\helpers\Json;
use yii\web\ViewAction as BaseAction;

use function preg_match;
use function sprintf;
use function str_replace;
use function substr;

use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_MAXREDIRS;

class StartRemoteFollowAction extends BaseAction
{
    public function init()
    {
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->language = 'ja-JP';
        parent::init();
    }

    public function run()
    {
        $form = Yii::createObject(RemoteFollowModalForm::class);
        if (!$form->load($_POST) || !$form->validate()) {
            //FIXME
            $resp = Yii::$app->response;
            $resp->format = 'json';
            return $form->getErrors();
        }
        if ($url = $this->discoverRemote($form->screen_name, $form->account, $form->host_name)) {
            if (preg_match('!^https?://!', $url)) {
                return $this->controller->redirect($url, 302);
            }
        }
    }

    private function discoverRemote(string $thisName, string $accountName, string $remoteHostName): ?string
    {
        try {
            if (!$webfinger = $this->discoverWebfingerPath($thisName, $accountName, $remoteHostName)) {
                return null;
            }
            if (!$url = $this->discoverSubscribeUrl($webfinger, $thisName, $accountName, $remoteHostName)) {
                return null;
            }
            return $url;
        } catch (Throwable $e) {
            return null;
        }
    }

    private function discoverWebfingerPath(string $thisName, string $accountName, string $remoteHostName): ?string
    {
        foreach (['https', 'http'] as $scheme) {
            try {
                $url = sprintf('%s://%s/.well-known/host-meta', $scheme, $remoteHostName);
                $ret = $this->get($url);
                if ($ret !== null) {
                    $doc = new DOMDocument();
                    if (!@$doc->loadXML($ret)) {
                        continue;
                    }
                    $xpath = new DOMXpath($doc);
                    $xpath->registerNamespace('xrd', 'http://docs.oasis-open.org/ns/xri/xrd-1.0');
                    foreach ($xpath->query('//xrd:Link') as $link) {
                        if ($link->getAttribute('rel') === 'lrdd') {
                            if (
                                $link->getAttribute('type') === 'application/xrd+xml' ||
                                $link->getAttribute('type') === 'application/lrd+json'
                            ) {
                                if ($link->hasAttribute('href')) {
                                    return $link->getAttribute('href');
                                } elseif ($link->hasAttribute('template')) {
                                    return str_replace(
                                        '{uri}',
                                        sprintf('acct:%s', $accountName),
                                        $link->getAttribute('template'),
                                    );
                                }
                            }
                        }
                    }
                }
            } catch (Throwable $e) {
            }
        }
        return null;
    }

    private function discoverSubscribeUrl(
        string $url,
        string $thisName,
        string $accountName,
        string $remoteHostName
    ): ?string {
        if (!$response = $this->get($url)) {
            return null;
        }
        try {
            if (substr($response, 0, 2) === '{"') {
                $json = Json::decode($response);
                if (isset($json['links'])) {
                    foreach ($json['links'] as $link) {
                        if (($link['rel'] ?? null) !== 'http://ostatus.org/schema/1.0/subscribe') {
                            continue;
                        }
                        if (isset($link['href']) && substr($link['href'], 0, 4) === 'http') {
                            return $link['href'];
                        }
                        if (isset($link['template']) && substr($link['template'], 0, 4) === 'http') {
                            return str_replace(
                                '{uri}',
                                sprintf('acct:%s@%s', $thisName, Yii::$app->request->hostName),
                                $link['template'],
                            );
                        }
                    }
                }
            }
        } catch (Throwable $e) {
        }
        //FIXME:XML
        return null;
    }

    private function get(string $url): ?string
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setOpt(CURLOPT_MAXREDIRS, 2);
        $curl->get($url);
        if ($curl->error) {
            return null;
        }
        return $curl->rawResponse;
    }
}
