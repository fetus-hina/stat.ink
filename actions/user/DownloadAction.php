<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function is_scalar;
use function sprintf;

class DownloadAction extends BaseAction
{
    private ?User $user = null;

    /**
     * @return array<string, mixed>|null
     */
    #[Override]
    public function run(): ?array
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

    /**
     * @return array<string, mixed>
     */
    private function runIkaLogCsv(): array
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-ikalog.csv', 'text/cvs; charset=Shift_JIS', false, null);
        $resp->format = 'csv';
        $battles = $this->user->getBattles()
            ->with(['rule', 'map'])
            ->orderBy('{{battle}}.[[id]] ASC');
        $generator = function () use ($battles) {
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

    /**
     * @return array<string, mixed>
     */
    private function runIkaLogJson(): array
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
        $generator = function () use ($battles) {
            foreach ($battles->each() as $battle) {
                yield $battle->toIkaLogJson();
            }
        };

        return [
            'rows' => $generator(),
        ];
    }

    private function runUserJson(): null
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

        return null;
    }
}
