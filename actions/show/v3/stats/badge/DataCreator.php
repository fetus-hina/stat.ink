<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats\badge;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\Rule3;
use app\models\SalmonBoss3;
use app\models\SalmonKing3;
use app\models\Special3;
use app\models\TricolorRole3;
use app\models\User;
use app\models\UserBadge3Adjust;
use app\models\UserBadge3BossSalmonid;
use app\models\UserBadge3KingSalmonid;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Special;
use app\models\UserBadge3Tricolor;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function filter_var;
use function is_int;
use function is_iterable;
use function is_string;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;

trait DataCreator
{
    private function createData(User $user): array
    {
        return TypeHelper::instanceOf(Yii::$app->db, Connection::class)->transaction(
            fn (Connection $db): array => [
                'badgeAdjust' => $this->getBadgeAdjust($db, $user),
                'badgeBosses' => $this->getBadgeBosses($db, $user),
                'badgeKings' => $this->getBadgeKings($db, $user),
                'badgeRules' => $this->getBadgeRules($db, $user),
                'badgeSpecials' => $this->getBadgeSpecials($db, $user),
                'badgeTricolor' => $this->getBadgeTricolor($db, $user),
                'bosses' => $this->getBosses($db),
                'kings' => $this->getKings($db),
                'roles' => $this->getRoles($db),
                'rules' => $this->getRules($db),
                'specials' => $this->getSpecials($db),
                'user' => $user,
            ],
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * @return array<string, int>
     */
    private function getBadgeAdjust(Connection $db, User $user): array
    {
        $model = UserBadge3Adjust::find()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->one($db);
        if (!$model) {
            return [];
        }

        if (is_iterable($model->data)) {
            return self::filterBadgeAdjustmentData($model->data);
        }

        if (is_string($model->data)) {
            $data = Json::decode($model->data);
            return is_iterable($data) ? self::filterBadgeAdjustmentData($data) : [];
        }

        return [];
    }

    /**
     * @return array<string, int>
     */
    private static function filterBadgeAdjustmentData(iterable $data): array
    {
        $results = [];
        foreach ($data as $k => $v) {
            $v = is_int($v) ? $v : filter_var($v, FILTER_VALIDATE_INT);
            if (is_string($k) && is_int($v)) {
                $results[$k] = $v;
            }
        }

        return $results;
    }

    /**
     * @return array<string, UserBadge3BossSalmonid>
     */
    private function getBadgeBosses(Connection $db, User $user): array
    {
        return ArrayHelper::map(
            UserBadge3BossSalmonid::find()
                ->with(['boss'])
                ->andWhere(['user_id' => $user->id])
                ->all($db),
            'boss.key',
            fn (UserBadge3BossSalmonid $v): UserBadge3BossSalmonid => $v,
        );
    }

    /**
     * @return array<string, UserBadge3KingSalmonid>
     */
    private function getBadgeKings(Connection $db, User $user): array
    {
        return ArrayHelper::map(
            UserBadge3KingSalmonid::find()
                ->with(['king'])
                ->andWhere(['user_id' => $user->id])
                ->all($db),
            'king.key',
            fn (UserBadge3KingSalmonid $v): UserBadge3KingSalmonid => $v,
        );
    }

    /**
     * @return array<string, UserBadge3Rule>
     */
    private function getBadgeRules(Connection $db, User $user): array
    {
        return ArrayHelper::map(
            UserBadge3Rule::find()
                ->with(['rule'])
                ->andWhere(['{{%user_badge3_rule}}.[[user_id]]' => $user->id])
                ->all($db),
            'rule.key',
            fn (UserBadge3Rule $v): UserBadge3Rule => $v,
        );
    }

    /**
     * @return array<string, UserBadge3Special>
     */
    private function getBadgeSpecials(Connection $db, User $user): array
    {
        return ArrayHelper::map(
            UserBadge3Special::find()
                ->with(['special'])
                ->andWhere(['user_id' => $user->id])
                ->all($db),
            'special.key',
            fn (UserBadge3Special $v): UserBadge3Special => $v,
        );
    }

    /**
     * @return array<string, UserBadge3Tricolor>
     */
    private function getBadgeTricolor(Connection $db, User $user): array
    {
        return ArrayHelper::map(
            UserBadge3Tricolor::find()
                ->with(['role'])
                ->andWhere(['user_id' => $user->id])
                ->all($db),
            'role.key',
            fn (UserBadge3Tricolor $v): UserBadge3Tricolor => $v,
        );
    }

    /**
     * @return SalmonBoss3[]
     */
    private function getBosses(Connection $db): array
    {
        return SalmonBoss3::find()
            ->andWhere(['has_badge' => true])
            ->orderBy(['rank' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return SalmonKing3[]
     */
    private function getKings(Connection $db): array
    {
        return SalmonKing3::find()
            ->orderBy(['id' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return TricolorRole3[]
     */
    private function getRoles(Connection $db): array
    {
        return TricolorRole3::find()
            ->orderBy(['id' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return Rule3[]
     */
    private function getRules(Connection $db): array
    {
        return Rule3::find()
            ->andWhere(['<>', 'key', 'tricolor'])
            ->orderBy(['rank' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return Special3[]
     */
    private function getSpecials(Connection $db): array
    {
        return Special3::find()
            ->orderBy(['rank' => SORT_ASC])
            ->all($db);
    }
}
