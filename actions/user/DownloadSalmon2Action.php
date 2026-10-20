<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Override;
use Yii;
use app\models\Salmon2;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction;

use function is_scalar;

use const SORT_ASC;

class DownloadSalmon2Action extends ViewAction
{
    private User $user;

    #[Override]
    public function init(): void
    {
        parent::init();
        if (!$user = Yii::$app->user->identity) {
            throw new ServerErrorHttpException();
        }
        $this->user = $user;
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
    {
        $type = Yii::$app->request->get('type');
        if (is_scalar($type)) {
            switch ((string)$type) {
                case 'csv':
                    return $this->runCsv();
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
    private function runCsv(): array
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('salmon.csv', 'text/cvs; charset=UTF-8', false, null);
        $resp->format = 'csv';
        $query = $this->user->getSalmonResults()
            ->with([
                'bossAppearances',
                'failReason',
                'players',
                'players.bossKills',
                'players.special',
                'players.specialUses',
                'players.weapons',
                'players.weapons.weapon',
                'stage',
                'titleAfter',
                'titleBefore',
                'waves',
                'waves.event',
                'waves.water',
            ])
            ->orderBy(['id' => SORT_ASC]);
        $generator = function () use ($query) {
            $schema = Salmon2::csvArraySchema();
            $schema[0] = '# ' . $schema[0];
            yield $schema;
            unset($schema);

            foreach ($query->each(250) as $result) {
                yield $result->toCsvArray();
            }
        };

        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => 'UTF-8',
            'appendBOM' => true,
            'rows' => $generator(),
        ];
    }
}
