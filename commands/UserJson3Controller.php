<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\components\helpers\UserExportJson3Helper;
use app\components\jobs\UserExportJson3Job;
use app\models\User;
use yii\console\Controller;
use yii\db\Query;

use function fwrite;
use function rtrim;
use function vfprintf;

use const SORT_ASC;
use const STDERR;

final class UserJson3Controller extends Controller
{
    public $defaultAction = 'auto-update';

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
        $select = (new Query())
          ->select(['user_id'])
          ->from('{{%battle3}}')
          ->groupBy(['user_id'])
          ->orderBy(['user_id' => SORT_ASC]);

        foreach ($select->each(200) as $row) {
            $user = User::find()
            ->andWhere(['id' => $row['user_id']])
            ->limit(1)
            ->one();
            if ($user) {
                UserExportJson3Job::pushQueue($user);
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

        UserExportJson3Helper::update($user);
        return 0;
    }
}
