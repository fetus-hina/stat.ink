<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Throwable;
use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\Slack;
use app\models\User;
use yii\base\BaseObject;
use yii\queue\JobInterface;

use const SORT_ASC;

class SlackJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public $hostInfo;
    public $version;
    public $battle;

    public function execute($queue)
    {
        if (isset($this->hostInfo)) {
            $urlManager = Yii::$app->getUrlManager();
            $urlManager->baseUrl = $this->hostInfo;
            $urlManager->hostInfo = $this->hostInfo;
        }

        switch ($this->version) {
            case 1:
                $battle = Battle::findOne(['id' => $this->battle]);
                if ($battle) {
                    $this->battle1($battle);
                }
                break;

            case 2:
                $battle = Battle2::findOne(['id' => $this->battle]);
                if ($battle) {
                    $this->battle2($battle);
                }
                break;

            default:
                Yii::error('Unsupported battle version ' . $this->version, __METHOD__);
                break;
        }
    }

    private function querySlackTask(User $user)
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

    public function battle1(Battle $battle): void
    {
        foreach ($this->querySlackTask($battle->user) as $slack) {
            try {
                $slack->send($battle);
            } catch (Throwable $e) {
            }
        }
    }

    public function battle2(Battle2 $battle): void
    {
        foreach ($this->querySlackTask($battle->user) as $slack) {
            try {
                $slack->send($battle);
            } catch (Throwable $e) {
            }
        }
    }
}
