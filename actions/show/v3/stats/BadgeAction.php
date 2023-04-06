<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use LogicException;
use Yii;
use app\models\Rule3;
use app\models\SalmonBoss3;
use app\models\SalmonKing3;
use app\models\Special3;
use app\models\TricolorRole3;
use app\models\User;
use app\models\UserBadge3BossSalmonid;
use app\models\UserBadge3KingSalmonid;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Special;
use app\models\UserBadge3Tricolor;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function assert;
use function is_string;

use const SORT_ASC;

final class BadgeAction extends Action
{
    public ?User $user = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $inputUserId = Yii::$app->request->get('screen_name');
        if (!is_string($inputUserId)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $this->user = User::find()
            ->andWhere(['screen_name' => $inputUserId])
            ->limit(1)
            ->one();

        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string
    {
        if (!($user = $this->user)) {
            throw new LogicException();
        }

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => [
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

        $c = $this->controller;
        assert($c instanceof Controller);
        return $c->render('stats/badge', $data);
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
            ->orderBy(['name' => SORT_ASC])
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
