<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands\bugfix;

use LogicException;
use Yii;
use app\components\helpers\RandomFilename;
use app\models\BattleImage;
use app\models\BattleImage2;
use app\models\UserIcon;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Expression;
use yii\helpers\FileHelper;

use function array_reverse;
use function assert;
use function count;
use function dirname;
use function file_exists;
use function fprintf;
use function is_file;
use function json_encode;
use function preg_quote;
use function preg_replace;
use function rename;
use function sprintf;
use function vfprintf;

use const SORT_ASC;
use const STDERR;

final class EqualsInFilenameAction extends Action
{
    private const TARGETS = [
        [
            'label' => 'battle_image',
            'modelClass' => BattleImage::class,
            'directoryAlias' => '@app/web/images',
            'extension' => 'jpg',
            'companionExtensions' => ['lep'],
            'hasBucket' => true,
        ],
        [
            'label' => 'battle_image2',
            'modelClass' => BattleImage2::class,
            'directoryAlias' => '@app/web/images',
            'extension' => 'jpg',
            'companionExtensions' => ['lep'],
            'hasBucket' => true,
        ],
        [
            'label' => 'user_icon',
            'modelClass' => UserIcon::class,
            'directoryAlias' => '@app/web/profile-images',
            'extension' => 'png',
            'companionExtensions' => [],
            'hasBucket' => false,
        ],
    ];

    /**
     * GH-908: christian-riesen/base32 にあった「パディング '=' を文字列の途中に
     * 出力する」バグで生成された画像ファイル名 (battle_image / battle_image2 /
     * user_icon) について、新しいランダムファイル名を払い出して付け替える。
     */
    public function run(): int
    {
        $dryRun = (bool)($this->controller->dryRun ?? false);
        if ($dryRun) {
            fprintf(STDERR, "[Info] Running in dry-run mode (no changes will be made)\n");
        }

        $failed = false;
        foreach (self::TARGETS as $target) {
            if (!$this->processTarget($target, $dryRun)) {
                $failed = true;
            }
        }

        return $failed ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    private function processTarget(array $target, bool $dryRun): bool
    {
        fprintf(STDERR, "[Info] Processing %s\n", $target['label']);

        /** @var class-string<ActiveRecord> $modelClass */
        $modelClass = $target['modelClass'];
        $directory = Yii::getAlias($target['directoryAlias']);
        assert($directory !== false);

        // PK-cursor iteration: updated rows leave the filtered set, and skipped
        // rows would otherwise be returned forever by plain LIMIT.
        $pkColumn = $modelClass::primaryKey()[0];

        // The bug can leave behind both "=" characters and short basenames
        // (trailing "a"s got rtrim'd along with real padding). So filter by
        // the negation of the canonical shape rather than just LIKE '%=%'.
        $canonicalPattern = '^[a-z2-7]{2}/[a-z2-7]{26}[.]' . $target['extension'] . '$';
        $filterExpr = new Expression('{{filename}} !~ :p', [':p' => $canonicalPattern]);

        $okCount = 0;
        $skipCount = 0;
        $failCount = 0;
        $lastPk = null;
        while (true) {
            $query = $modelClass::find()
                ->andWhere($filterExpr)
                ->orderBy([$pkColumn => SORT_ASC])
                ->limit(500);
            assert($query instanceof ActiveQuery);
            if ($lastPk !== null) {
                $query->andWhere(['>', $pkColumn, $lastPk]);
            }
            $batch = $query->all();
            if ($batch === []) {
                break;
            }
            foreach ($batch as $model) {
                assert($model instanceof ActiveRecord);
                $status = $this->processRow($model, $target, $directory, $dryRun);
                match ($status) {
                    'ok' => ++$okCount,
                    'skip' => ++$skipCount,
                    'fail' => ++$failCount,
                };
                $lastPk = $model->$pkColumn;
            }
        }

        fprintf(STDERR, "[Info] %s: %d fixed, %d skipped, %d failed\n", $target['label'], $okCount, $skipCount, $failCount);
        return $failCount === 0;
    }

    /**
     * @return string 'ok' | 'skip' | 'fail'
     */
    private function processRow(
        ActiveRecord $model,
        array $target,
        string $directory,
        bool $dryRun,
    ): string {
        $oldFilename = (string)$model->filename;

        // Only the default local bucket can be moved on disk by this action.
        if ($target['hasBucket'] && (int)$model->bucket_id !== 1) {
            vfprintf(STDERR, "[Warn] %s: skip %s (non-default bucket_id=%s)\n", [
                $target['label'],
                $oldFilename,
                (string)$model->bucket_id,
            ]);
            return 'skip';
        }

        $primarySuffix = '.' . $target['extension'];
        $primaryOld = $directory . '/' . $oldFilename;
        if (!file_exists($primaryOld) || !is_file($primaryOld)) {
            vfprintf(STDERR, "[Warn] %s: skip %s (file not found at %s)\n", [
                $target['label'],
                $oldFilename,
                $primaryOld,
            ]);
            return 'skip';
        }

        // Existing companion files (.lep next to .jpg) need to come along.
        $companionOldPaths = [];
        foreach ($target['companionExtensions'] as $companionExt) {
            $companionSuffix = '.' . $companionExt;
            $oldCompanion = $directory . '/' . $this->replaceExt(
                $oldFilename,
                $primarySuffix,
                $companionSuffix,
            );
            if (file_exists($oldCompanion) && is_file($oldCompanion)) {
                $companionOldPaths[$companionSuffix] = $oldCompanion;
            }
        }

        $newFilename = $this->generateNewFilename($target);

        $moves = [
            $primarySuffix => [
                'old' => $primaryOld,
                'new' => $directory . '/' . $newFilename,
            ],
        ];
        foreach ($companionOldPaths as $companionSuffix => $oldCompanion) {
            $moves[$companionSuffix] = [
                'old' => $oldCompanion,
                'new' => $directory . '/' . $this->replaceExt(
                    $newFilename,
                    $primarySuffix,
                    $companionSuffix,
                ),
            ];
        }

        // Destination must not exist; otherwise we'd clobber an unrelated file.
        // generateNewFilename() guards against DB collisions; this guards
        // against orphan files left on disk.
        foreach ($moves as $pair) {
            if (file_exists($pair['new'])) {
                vfprintf(STDERR, "[Warn] %s: skip %s (destination %s already exists)\n", [
                    $target['label'],
                    $oldFilename,
                    $pair['new'],
                ]);
                return 'skip';
            }
        }

        if ($dryRun) {
            vfprintf(STDERR, "[Dry] %s: would move %s -> %s (%d file%s)\n", [
                $target['label'],
                $oldFilename,
                $newFilename,
                count($moves),
                count($moves) === 1 ? '' : 's',
            ]);
            return 'ok';
        }

        return $this->applyFix($model, $target, $oldFilename, $newFilename, $moves)
            ? 'ok'
            : 'fail';
    }

    private function generateNewFilename(array $target): string
    {
        $modelClass = $target['modelClass'];
        if ($modelClass === BattleImage::class || $modelClass === BattleImage2::class) {
            // BattleImage::generateFilename internally checks both
            // battle_image and battle_image2 for uniqueness.
            return BattleImage::generateFilename();
        }
        if ($modelClass === UserIcon::class) {
            while (true) {
                $candidate = RandomFilename::generate('png', 1);
                $exists = UserIcon::find()
                    ->andWhere(['filename' => $candidate])
                    ->exists();
                if (!$exists) {
                    return $candidate;
                }
            }
        }
        throw new LogicException(sprintf('No generator wired for model %s', $modelClass));
    }

    private function replaceExt(string $filename, string $fromSuffix, string $toSuffix): string
    {
        return preg_replace(
            '/' . preg_quote($fromSuffix, '/') . '$/',
            $toSuffix,
            $filename,
        );
    }

    private function applyFix(
        ActiveRecord $model,
        array $target,
        string $oldFilename,
        string $newFilename,
        array $moves,
    ): bool {
        // Ensure target directory exists for every move.
        foreach ($moves as $pair) {
            FileHelper::createDirectory(dirname($pair['new']));
        }

        // Move files first, tracking each successful move so we can roll back
        // if the DB update fails afterwards.
        $movedOrder = [];
        foreach ($moves as $ext => $pair) {
            if (!@rename($pair['old'], $pair['new'])) {
                vfprintf(STDERR, "[Error] %s: failed to rename %s -> %s\n", [
                    $target['label'],
                    $pair['old'],
                    $pair['new'],
                ]);
                $this->rollbackFileMoves($movedOrder, $moves);
                return false;
            }
            $movedOrder[] = $ext;
        }

        $model->filename = $newFilename;

        $db = Yii::$app->db;
        assert($db instanceof Connection);
        $saved = $db->transaction(static fn (): bool => $model->save());
        if (!$saved) {
            vfprintf(STDERR, "[Error] %s: failed to save model (%s -> %s): %s\n", [
                $target['label'],
                $oldFilename,
                $newFilename,
                json_encode($model->getErrors()),
            ]);
            $this->rollbackFileMoves($movedOrder, $moves);
            return false;
        }

        vfprintf(STDERR, "[Info] %s: moved %s -> %s\n", [
            $target['label'],
            $oldFilename,
            $newFilename,
        ]);
        return true;
    }

    /**
     * @param string[] $movedOrder Keys of $moves that were successfully renamed.
     * @param array<string, array{old: string, new: string}> $moves
     */
    private function rollbackFileMoves(array $movedOrder, array $moves): void
    {
        foreach (array_reverse($movedOrder) as $ext) {
            $pair = $moves[$ext];
            if (!@rename($pair['new'], $pair['old'])) {
                vfprintf(STDERR, "[Error] rollback failed: %s -> %s (manual cleanup needed)\n", [
                    $pair['new'],
                    $pair['old'],
                ]);
            }
        }
    }
}
