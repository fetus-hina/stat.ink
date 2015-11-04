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
    const ARCHIVE_SPLIT_AIM = 524288000; // 500 MiB

    public function init()
    {
        Yii::setAlias('@archive', Yii::getAlias('@app/runtime/image-archive'));
        return parent::init();
    }

    // optimize {{{
    public function actionOptimize()
    {
        $inDir  = Yii::getAlias('@archive/queue');
        $outDir = Yii::getAlias('@archive/interim');

        if (!file_exists($inDir)) {
            $this->stderr(
                "Optimize queue directory not found. It will be created on post a battle.\n",
                Console::FG_RED
            );
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
    // }}}

    // archive {{{
    public function actionArchive($date = null)
    {
        if ($date === null) {
            $tmp = time() + 9 * 3600;
            $tmp = gmmktime(0, 0, 0, gmdate('n', $tmp), gmdate('j', $tmp) - 1, gmdate('Y', $tmp));
            $date = gmdate('Ymd', $tmp);
        }

        if (!preg_match('/^\d{8}$/', $date)) {
            $this->stderr("Invalid date given. YYYYMMDD required.\n", Console::FG_RED);
            return 1;
        }

        $inDirs = [
            'judge'  => Yii::getAlias('@archive/interim') . '/' . $date . '-judge',
            'result' => Yii::getAlias('@archive/interim') . '/' . $date . '-result',
        ];
        $outDir = Yii::getAlias('@archive/archive');

        $inDirExists = false;
        foreach ($inDirs as $tmp) {
            if (!file_exists($tmp)) {
                $this->stderr("Input directory not found. {$tmp}\n", Console::FG_RED);
            } else {
                $inDirExists = true;
            }
        }
        if (!$inDirExists) {
            return 1;
        }

        if (!$lock = $this->getLock(__METHOD__)) {
            $this->stderr("Another process is running.\n");
            return 0;
        }

        $plans = $this->createArchivePlan($inDirs, $outDir, $date);
        if (empty($plans)) {
            $this->stderr("[archive] No plans created.\n");
            return 0;
        }

        $this->stdout(sprintf("[archive] %d plans created.\n", count($plans)), Console::FG_GREEN);

        $error = false;
        foreach ($plans as $plan) {
            if (!$this->executeArchivePlan($plan)) {
                $error = true;
            }
        }

        if (!$error) {
            foreach ($inDirs as $dir) {
                $this->rmdirRecursive($dir);
            }
        }

        return $error ? 1 : 0;
    }

    private function createArchivePlan(array $dirs, $outDir, $date)
    {
        $this->stdout("[archive] Creating plan...\n");
        // $fileLists = [
        //     'result' => [
        //         (object)[
        //             'path' => '/path/to/file.png',
        //             'size' => 42,
        //         ],
        //         ...
        //     ],
        //     'judge' => [ ... ],
        // ]
        $fileLists = array_map(
            function ($dir) {
                return $this->getFileInfoListForArchivePlan($dir);
            },
            $dirs
        );

        $ret = [];

        // 走査開始番号（一番若いバトルID）
        $startId = min(
            array_map(
                function (array $files) {
                    return count($files) ? $files[0]->battle : PHP_INT_MAX;
                },
                $fileLists
            )
        );
        $part = 1;
        while ($startId < PHP_INT_MAX) {
            // 規定容量程度になるように分割するときの最終バトルID
            $endId = min(
                array_map(
                    function (array $files) use ($startId) {
                        $files = array_filter(
                            $files,
                            function ($file) use ($startId) {
                                return $file->battle >= $startId;
                            }
                        );
                        $total = 0;
                        foreach ($files as $file) {
                            $total += $file->size;
                            if ($total >= self::ARCHIVE_SPLIT_AIM) {
                                return $file->battle;
                            }
                        }
                        return PHP_INT_MAX - 1; // 後で + 1 するので INT を超えないように
                    },
                    $fileLists
                )
            );

            $this->stdout(sprintf("    Archive #%d : battle %d - %d\n", $part, $startId, $endId));
            foreach ($dirs as $key => $dir) {
                $files = array_filter(
                    $fileLists[$key],
                    function ($file) use ($startId, $endId) {
                        return $file->battle >= $startId && $file->battle <= $endId;
                    }
                );
                if (!empty($files)) {
                    $ret[] = (object)[
                        'dst'    => sprintf('%s/%s-%02d-%s.tar', $outDir, $date, $part, $key),
                        'srcDir' => $dir,
                        'srcFiles' => array_values(
                            array_map(
                                function ($info) {
                                    return basename($info->path);
                                },
                                $files
                            )
                        ),
                    ];
                }
            }

            ++$part;
            $startId = $endId + 1;
        }
        return $ret;
    }

    private function getFileInfoListForArchivePlan($dir)
    {
        $ret = [];
        foreach (new DirectoryIterator($dir) as $entry) {
            if ($entry->isFile() && preg_match('/^(\d+)-\w+\.png$/', $entry->getBasename(), $match)) {
                $ret[] = (object)[
                    'battle' => (int)$match[1],
                    'path' => $entry->getPathname(),
                    'size' => $entry->getSize(),
                ];
            }
        }
        usort(
            $ret,
            function ($a, $b) {
                return strnatcmp($a->path, $b->path);
            }
        );
        return $ret;
    }

    private function executeArchivePlan(\stdClass $plan)
    {
        $this->stdout("[archive] Creating archive " . basename($plan->dst) . " ... ", Console::FG_YELLOW);

        if (!file_exists(dirname($plan->dst))) {
            mkdir(dirname($plan->dst), 0755, true);
        }
        if (file_exists($plan->dst)) {
            $this->stdout("ALREADY EXISTS\n", Console::FG_YELLOW);
            return true;
        }

        // tar に喰わせるためのファイルリストを作成
        $tmpFile = new Resource(
            tempnam(Yii::getAlias('@archive'), 'tmp-'),
            'unlink'
        );
        file_put_contents($tmpFile->get(), implode("\n", $plan->srcFiles) . "\n");

        // tar の実行
        $chdir = new Resource(
            getcwd(),
            function ($dir) {
                chdir($dir);
            }
        );
        chdir($plan->srcDir);
        $cmdline = sprintf(
            '/usr/bin/env %s -c -T %s -f %s',
            escapeshellarg('tar'),
            escapeshellarg($tmpFile->get()),
            escapeshellarg($plan->dst)
        );
        $lines = $status = null;
        exec($cmdline, $lines, $status);
        if ($status === 0) {
            $this->stdout("SUCCESS\n", Console::FG_GREEN);
            return true;
        }
        $this->stderr("FAILED with code={$status}\n", Console::FG_RED);
        return false;
    }
    // }}}

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

    protected function rmdirRecursive($dir)
    {
        $files = [];
        $subs = [];
        foreach (new DirectoryIterator($dir) as $entry) {
            if ($entry->isDot()) {
                continue;
            }
            if ($entry->isDir()) {
                $subs[] = $dir . '/' . $entry->getBasename();
            } else {
                $files[] = $dir . '/' . $entry->getBasename();
            }
        }

        foreach ($files as $path) {
            $this->stdout("delete file: " . $path . "\n");
            unlink($path);
        }

        foreach ($subs as $path) {
            $this->rmdirRecursive($path);
        }

        $this->stdout("delete directory: " . $dir . "\n");
        rmdir($dir);
    }
}
