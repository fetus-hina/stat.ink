<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Generator;
use Throwable;
use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Slack;
use app\models\User;
use yii\base\BaseObject;
use yii\queue\JobInterface;

use const SORT_ASC;

class SlackJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public string|null $hostInfo = null;
    public int|null $version = null;
    public int|null $battle = null;

    public function execute($queue)
    {
        if (isset($this->hostInfo)) {
            $urlManager = Yii::$app->getUrlManager();
            $urlManager->baseUrl = $this->hostInfo;
            $urlManager->hostInfo = $this->hostInfo;
        }

        match ($this->version) {
            1 => self::notifyToSlack(
                Battle::find()->andWhere(['id' => $this->battle])->limit(1)->one(),
            ),
            2 => self::notifyToSlack(
                Battle2::find()->andWhere(['id' => $this->battle])->limit(1)->one(),
            ),
            3 => self::notifyToSlack(
                Battle3::find()
                    ->andWhere([
                        'id' => $this->battle,
                        'is_deleted' => false,
                    ])
                    ->limit(1)
                    ->one(),
            ),
            default => Yii::error('Unsupported battle version ' . $this->version, __METHOD__),
        };
    }

    public static function notifyToSlack(Battle|Battle2|Battle3|null $battle): void
    {
        // make this method public for testing

        if (!$battle) {
            return;
        }

        foreach (self::querySlackTask($battle->user) as $slack) {
            try {
                $slack->send($battle);
            } catch (Throwable $e) {
            }
        }
    }

    /**
     * @return Generator<Slack>
     */
    private static function querySlackTask(User $user): Generator
    {
        $query = Slack::find()
            ->with('user')
            ->andWhere([
                'user_id' => $user->id,
                'suspended' => false,
            ])
            ->orderBy(['id' => SORT_ASC]);
        foreach ($query->each() as $slack) {
            yield $slack;
        }
    }
}
