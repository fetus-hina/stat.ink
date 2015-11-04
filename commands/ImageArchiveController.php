<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use DirectoryIterator;
use S3;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;

class ImageArchiveController extends Controller
{
    const MAX_OPTIMIZE_PROC_COUNT = 3;

    public function init()
    {
        Yii::setAlias('@archive', Yii::getAlias('@app/runtime/image-archive'));
        return parent::init();
    }

    public function actionOptimize()
    {
        $inDir  = Yii::getAlias('@archive/queue');
        $outDir = Yii::getAlias('@archive/interim');

        if (!file_exists($inDir)) {
            $this->stderr("Optimize queue directory not found. It will be created on post a battle.\n", Console::FG_RED);
            return 1;
        }

        if (!$lock = $this->getLock(__METHOD__)) {
            $this->stderr("Another process is running.\n");
            return 0;
        }

        $hasError = false;
        foreach (new DirectoryIterator($inDir) as $entry) {
            if ($entry->isDot()) {
                continue;
            }
            $this->stdout("[optimize] " . $entry->getBasename() . "\n");
            if (!preg_match('/^\d{8}$/', $entry->getBasename())) {
                $this->stdout("    SKIP\n", Console::FG_YELLOW);
                continue;
            }
            $ret = $this->runOptimize(
                $entry->getBaseName(),
                $inDir . '/' . $entry->getBaseName(),
                $outDir
            );
            if ($ret) {
                $this->stdout("    SUCCESS\n", Console::FG_GREEN);
            } else {
                $this->stdout("    ERROR\n", Console::FG_RED);
                $hasError = true;
            }
        }
        return $hasError ? 1 : 0;
    }

    private function runOptimize($date, $inDir, $outBaseDir)
    {
        $queue = [];
        foreach (new DirectoryIterator($inDir) as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }
            if (preg_match('/^\d+-(result|judge)\.png$/', $entry->getBasename(), $match)) {
                $outDir = sprintf('%s/%s-%s', $outBaseDir, $date, $match[1]);
                if (!file_exists($outDir)) {
                    if (!mkdir($outDir, 0755, true)) {
                        return false;
                    }
                }
                $queue[] = (object)[
                    'input' => $entry->getPathname(),
                    'output' => $outDir . '/' . $entry->getBasename(),
                ];
            }
        }

        $procs = [];
        $error = false;
        while (!$error && !empty($queue)) {
            // 規定のプロセス数になるまでプロセスを作る
            while (!$error &&
                    !empty($queue) &&
                    count($procs) < self::MAX_OPTIMIZE_PROC_COUNT
            ) {
                $task = array_shift($queue);
                if (!$tmp = $this->startOptimizeSubProcess($task)) {
                    $error = true;
                } else {
                    $procs[] = $tmp;
                }
            }

            // サブプロセス実行中・終了確認
            $terminated = 0;
            foreach ($procs as $i => $proc) {
                $tmp = $this->procOptimizeSubProcess($proc);
                if ($tmp === 'exit' || $tmp === 'error') {
                    unset($procs[$i]);
                    ++$terminated;
                    if ($tmp === 'error') {
                        $error = true;
                    }
                }
            }
            // プロセスリストの詰め直し
            if ($terminated > 0) {
                $procs = array_values($procs);
            }
            usleep(200 * 1000);
        }

        // キューは空になったがまだ子はいる(かもしれない)ので終了待ち
        while (!empty($procs)) {
            $terminated = 0;
            foreach ($procs as $i => $proc) {
                $tmp = $this->procOptimizeSubProcess($proc);
                if ($tmp === 'exit' || $tmp === 'error') {
                    unset($procs[$i]);
                    ++$terminated;
                    if ($tmp === 'error') {
                        $error = true;
                    }
                }
            }
            // プロセスリストの詰め直し
            if ($terminated > 0) {
                $procs = array_values($procs);
            }
            usleep(200 * 1000);
        }

        return !$error;
    }

    // 最適化する子プロセスを起こしてプロセス情報を返す
    private function startOptimizeSubProcess(\stdClass $task)
    {
        $this->stdout(sprintf("        %s: Optimizing\n", basename($task->input)));
        $cmdline = sprintf(
            '/usr/bin/env %s -rem allb -l 9 %s %s >/dev/null 2>&1',
            escapeshellarg('pngcrush'),
            escapeshellarg($task->input),
            escapeshellarg($task->output)
        );
        $descSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
        ];
        $pipes = null;
        if (!$proc = @proc_open($cmdline, $descSpec, $pipes)) {
            $this->stderr("          ERROR: Unable to create child process.\n", Console::FG_RED);
            return false;
        }
        $res = new Resource($proc, 'proc_close');
        fclose($pipes[0]);
        $pipes[0] = null;
        return (object)[
            'handle' => $res,
            'pipes' => $pipes,
            'input' => $task->input,
            'output' => $task->output,
        ];
    }

    // 実行中判定・終了時にファイル削除等
    private function procOptimizeSubProcess(\stdClass $proc)
    {
        $status = proc_get_status($proc->handle->get());
        if (!$status['running']) {
            @fclose($proc->pipes[1]);
            if ($status['exitcode'] !== 0) {
                $this->stdout(
                    sprintf(
                        "        %s: Failed. exit=%d\n",
                        basename($proc->input),
                        $status['exitcode']
                    ),
                    Console::FG_RED
                );
                return 'error';
            }
            $this->stdout(
                sprintf(
                    "        %s: Optimized. %s -> %s\n",
                    basename($proc->input),
                    filesize($proc->input),
                    filesize($proc->output)
                ),
                Console::FG_GREEN
            );
            unlink($proc->input);
            return 'exit';
        }
        return 'running';
    }

    protected function getLock($ident)
    {
        $lockPath = Yii::getAlias('@archive') . '/.lock-' . hash('crc32b', $ident);
        if (!file_exists(dirname($lockPath))) {
            mkdir(dirname($lockPath), 0755, true);
        }
        if (!$fh = fopen($lockPath, 'c')) {
            return false;
        }
        if (!flock($fh, LOCK_EX | LOCK_NB)) {
            fclose($fh);
            return false;
        }
        return new Resource(
            $fh,
            function ($fh) {
                flock($fh, LOCK_UN);
                fclose($fh);
            }
        );
    }
}
