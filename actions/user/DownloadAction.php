<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

class DownloadAction extends BaseAction
{
    private $user;

    public function run()
    {
        $this->user = Yii::$app->user->getIdentity();

        $type = Yii::$app->request->get('type');
        if (is_scalar($type)) {
            switch ((string)$type) {
                case 'ikalog-csv':
                    return $this->runIkaLogCsv();
                case 'ikalog-json':
                    return $this->runIkaLogJson();
                case 'user-json':
                    return $this->runUserJson();
            }
        }
        throw new BadRequestHttpException(
            Yii::t(
                'yii',
                'Invalid data received for parameter "{param}".',
                ['param' => 'type'],
            ),
        );
    }

    private function runIkaLogCsv()
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-ikalog.csv', 'text/cvs; charset=Shift_JIS', false, null);
        $resp->format = 'csv';
        $battles = $this->user->getBattles()
            ->with(['rule', 'map'])
            ->orderBy('{{battle}}.[[id]] ASC');
        $generator =  function () use ($battles) {
            foreach ($battles->each() as $battle) {
                yield $battle->toIkaLogCsv();
            }
        };

        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => 'CP932',
            'rows' => $generator(),
        ];
    }

    private function runIkaLogJson()
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-ikalog.json', 'application/octet-stream', false, null);
        $resp->format = 'ikalog-json';
        $battles = $this->user->getBattles()
            ->with([
                'rule', 'map', 'weapon', 'rank', 'rankAfter',
                'battlePlayers', 'battlePlayers.rank', 'battlePlayers.weapon',
            ])
            ->orderBy('{{battle}}.[[id]] ASC');
        $generator =  function () use ($battles) {
            foreach ($battles->each() as $battle) {
                yield $battle->toIkaLogJson();
            }
        };

        return [
            'rows' => $generator(),
        ];
    }

    private function runUserJson()
    {
        if (!$this->user->getIsUserJsonReady()) {
            throw new BadRequestHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        Yii::$app->response
            ->sendFile(
                $this->user->getUserJsonPath(),
                sprintf('statink-%s.json.gz', $this->user->screen_name),
                [
                    'mimeType' => 'application/octet-stream',
                    'inline' => false,
                ],
            )
            ->send();
    }
}
