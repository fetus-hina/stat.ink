<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\OstatusPubsubhubbub;
use yii\base\BaseObject;
use yii\queue\JobInterface;

use const SORT_ASC;

class OstatusJob extends BaseObject implements JobInterface
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

    public function battle1(Battle $battle): void
    {
        // @phpstan-ignore-next-line
        $query = OstatusPubsubhubbub::find()
            ->active()
            ->andWhere(['topic' => $battle->user_id])
            ->orderBy(['id' => SORT_ASC]);
        foreach ($query->each() as $hub) {
            try {
                $hub->notify($battle);
            } catch (\Throwable $e) {
            }
        }
    }

    public function battle2(Battle2 $battle): void
    {
        //FIXME
    }
}
