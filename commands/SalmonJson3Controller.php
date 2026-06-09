<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\components\helpers\SalmonExportJson3Helper;
use app\components\helpers\SalmonExportJson3QueueService;
use app\models\QueueSalmonExportJson3;
use app\models\User;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

use function fwrite;
use function hrtime;
use function pcntl_async_signals;
use function pcntl_signal;
use function rtrim;
use function vfprintf;
use function vsprintf;

use const SIGINT;
use const SIGTERM;
use const SORT_ASC;
use const STDERR;

final class SalmonJson3Controller extends Controller
{
    /**
     * 二重起動を防ぐためのプロセスロック名。
     */
    private const PROCESS_MUTEX_NAME = 'salmon-json3/process-queue';

    /**
     * 1 プロセスの最大稼働時間 (秒)。これを超えたら次のアイテムには進まず終了する。
     */
    private const TIME_LIMIT_SEC = 600;

    /**
     * キューに積まれてからこの秒数が経過するまではアイテムを処理しない。
     */
    private const ENQUEUE_GRACE_SEC = 10;

    public $defaultAction = 'auto-update';

    private bool $stopRequested = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@web', rtrim(Yii::$app->urlManager->baseUrl, '/'));
    }

    public function actionAutoUpdate(): int
    {
        $select = new Query()
          ->select(['user_id'])
          ->from('{{%salmon3}}')
          ->groupBy(['user_id'])
          ->orderBy(['user_id' => SORT_ASC]);

        foreach ($select->each(200) as $row) {
            $user = User::find()
            ->andWhere(['id' => $row['user_id']])
            ->limit(1)
            ->one();
            if ($user) {
                SalmonExportJson3QueueService::enqueue($user);
                vfprintf(STDERR, "%s(): push queue %d\n", [
                    __METHOD__,
                    $user->id,
                ]);
            }
        }

        return 0;
    }

    public function actionUpdate(int $id): int
    {
        $user = User::find()
            ->andWhere(['id' => $id])
            ->limit(1)
            ->one();
        if (!$user) {
            fwrite(STDERR, "Failed to find user {$id}\n");
            return 1;
        }

        SalmonExportJson3Helper::update($user);
        return 0;
    }

    /**
     * キューを監視し、空になるまで (最大 self::TIME_LIMIT_SEC 秒) 処理する。
     * systemd timer / cron からの起動を想定。二重起動は mutex で防ぎ、後発の
     * プロセスは即座に終了する。
     */
    public function actionProcessQueue(): int
    {
        $mutex = Yii::$app->pgMutex;
        if (!$mutex->acquire(self::PROCESS_MUTEX_NAME)) {
            fwrite(STDERR, "Another process is already running. Exiting.\n");
            return 0;
        }

        $this->stopRequested = false;
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, $this->onStopSignal(...));
        pcntl_signal(SIGINT, $this->onStopSignal(...));

        $startedAt = hrtime(true);

        // このラン中に失敗したアイテムの id。同じランでは再試行せずスキップする
        // (別プロセスでは記憶しないため再実行される)。
        $skipIds = [];
        $processed = 0;

        try {
            while (!$this->stopRequested) {
                $elapsedSec = (hrtime(true) - $startedAt) / 1_000_000_000;
                if ($elapsedSec >= self::TIME_LIMIT_SEC) {
                    break;
                }

                $queueItem = $this->fetchNextQueueItem($skipIds);
                if ($queueItem === null) {
                    break;
                }

                if (SalmonExportJson3QueueService::execute($queueItem)) {
                    // 処理中に enqueue されると id が変わり一致しなくなるため、
                    // その場合は削除されず次のランで再処理される。
                    QueueSalmonExportJson3::deleteAll([
                        'id' => $queueItem->id,
                        'user_id' => $queueItem->user_id,
                    ]);
                    ++$processed;
                } else {
                    $skipIds[] = $queueItem->id;
                }
            }
        } finally {
            $mutex->release(self::PROCESS_MUTEX_NAME);
        }

        vfprintf(STDERR, "%s(): finished, processed %d item(s)\n", [
            __METHOD__,
            $processed,
        ]);
        return 0;
    }

    /**
     * 処理対象の次の 1 件を取得する。十分に時間が経過し、かつこのランで失敗
     * していないアイテムのうち、最も古くキューに積まれたものを返す。
     *
     * @param list<string> $skipIds
     */
    private function fetchNextQueueItem(array $skipIds): ?QueueSalmonExportJson3
    {
        $query = QueueSalmonExportJson3::find()
            ->andWhere(new Expression(
                vsprintf('[[updated_at]] <= CURRENT_TIMESTAMP - make_interval(secs => %d)', [
                    self::ENQUEUE_GRACE_SEC,
                ]),
            ))
            ->orderBy(['updated_at' => SORT_ASC, 'id' => SORT_ASC])
            ->limit(1);

        if ($skipIds !== []) {
            $query->andWhere(['not in', 'id', $skipIds]);
        }

        $item = $query->one();
        return $item instanceof QueueSalmonExportJson3 ? $item : null;
    }

    private function onStopSignal(int $signo): void
    {
        $this->stopRequested = true;
    }
}
