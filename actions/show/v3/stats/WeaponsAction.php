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
use app\models\Battle3;
use app\models\Battle3FilterForm;
use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

use function assert;

use const SORT_ASC;

final class WeaponsAction extends Action
{
    public ?User $user = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $request = Yii::$app->request;
        assert($request instanceof Request);

        $this->user = User::find()
            ->andWhere(['screen_name' => $request->get('screen_name')])
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

        $form = Yii::createObject(Battle3FilterForm::class);
        if ($form->load($_GET)) {
            $form->validate();
        }

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => [
                'filter' => $form,
                'rules' => $this->getRules($db),
                'stats' => $this->makeStats($db, $user, $form),
                'user' => $user,
                'weapons' => $this->getWeapons($db),
            ],
            Transaction::REPEATABLE_READ,
        );

        $c = $this->controller;
        assert($c instanceof Controller);
        return $c->render('stats/weapons', $data);
    }

    /**
     * @return array<string, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()->orderBy(['rank' => SORT_ASC])->all($db),
            'key',
            fn (Rule3 $v): Rule3 => $v,
        );
    }

    /**
     * @return array<string, Weapon3>
     */
    private function getWeapons(Connection $db): array
    {
        return ArrayHelper::map(
            Weapon3::find()
              ->with(['subweapon', 'special'])
              ->orderBy(['name' => SORT_ASC])
              ->all($db),
            'key',
            fn (Weapon3 $v): Weapon3 => $v,
        );
    }

    /**
     * @return array<string, array<string, array>> `[weaponKey => [ruleKey => data]]`
     */
    private function makeStats(Connection $db, User $user, Battle3FilterForm $form): array
    {
        $q = Battle3::find()
            ->asArray()
            ->innerJoinWith(
                [
                    'lobby',
                    'map',
                    'result',
                    'rule',
                    'weapon',
                ],
                false,
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[lobby_id]]' => null]],
                ['not', ['{{%battle3}}.[[result_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle3}}.[[weapon_id]]' => null]],
            ])
            ->groupBy([
                '{{%battle3}}.[[user_id]]',
                '{{%battle3}}.[[rule_id]]',
                '{{%battle3}}.[[weapon_id]]',
            ])
            ->select([
                'weapon_id' => '{{%battle3}}.[[weapon_id]]',
                'weapon_key' => 'MAX({{%weapon3}}.[[key]])',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'rule_key' => 'MAX({{%rule3}}.[[key]])',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(CASE WHEN {{%result3}}.[[is_win]] THEN 1 ELSE 0 END)',
                'kills' => 'AVG({{%battle3}}.[[kill]])',
                'kill_stddev' => 'STDDEV_POP({{%battle3}}.[[kill]])',
                'deaths' => 'AVG({{%battle3}}.[[death]])',
                'death_stddev' => 'STDDEV_POP({{%battle3}}.[[death]])',
            ]);
        $form->decorateQuery($q);

        return ArrayHelper::map(
            $q->all($db),
            'rule_key',
            fn (array $row): array => $row,
            'weapon_key',
        );
    }
}
