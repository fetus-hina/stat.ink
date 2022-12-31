<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use Exception;
use Throwable;
use Yii;

class Connection extends \yii\db\Connection
{
    public function setTimezone(string $timeZone): void
    {
        $this->createCommand(
            $this->createCommand('SET TIMEZONE TO :tz')
                ->bindValue(':tz', $timeZone)
                ->rawSql,
        )->execute();
    }

    public function transactionEx(
        callable $callback,
        $rollBack = null,
        $isolationLevel = null
    ) {
        if ($rollBack === null) {
            $rollBack = [false, null];
        }

        $transaction = $this->beginTransaction($isolationLevel);
        $level = $transaction->level;
        try {
            $result = call_user_func($callback, $this, $transaction);
            if ($transaction->isActive && $transaction->level === $level) {
                if (
                    is_array($rollBack) &&
                    !is_callable($rollBack) &&
                    in_array($result, $rollBack, true)
                ) {
                    $transaction->rollBack();
                    return $result;
                } elseif (
                    is_callable($rollBack) &&
                    call_user_func($rollBack, $result, $this)
                ) {
                    $transaction->rollBack();
                    return $result;
                } else {
                    $transaction->commit();
                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->rollbackTransactionOnLevel($transaction, $level);
            throw $e;
        } catch (Throwable $e) {
            $this->rollbackTransactionOnLevel($transaction, $level);
            throw $e;
        }

        return $result;
    }

    private function rollbackTransactionOnLevel($transaction, $level)
    {
        if ($transaction->isActive && $transaction->level === $level) {
            try {
                $transaction->rollBack();
            } catch (Exception $e) {
                Yii::error($e, __METHOD__);
            }
        }
    }
}
