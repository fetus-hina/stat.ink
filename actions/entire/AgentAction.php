<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Base32\Base32;
use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;
use app\models\AgentGroup;
use app\models\StatAgentUser;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

use const SORT_ASC;

/**
 * @property-read AgentGroup[] $combineds
 * @property-read array[] $postStats
 */
final class AgentAction extends Action
{
    public $form;

    public function init()
    {
        parent::init();

        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        $form = new DynamicModel(['b32name' => Yii::$app->request->get('b32name')]);
        $form->addRule('b32name', 'required')
            ->addRule('b32name', 'match', ['pattern' => '/^[a-zA-Z2-7]+$/'])
            ->addRule('b32name', function ($attr, $conf) use ($form) {
                $decoded = Base32::decode($form->$attr);
                if ($decoded === '') {
                    $form->addError($attr, 'invalid name');
                    return;
                }
                if (!mb_check_encoding($decoded, 'UTF-8')) {
                    $form->addError($attr, 'broken encoding');
                    return;
                }
                if (!StatAgentUser::findOne(['agent' => $decoded])) {
                    $form->addError($attr, 'not found');
                    return;
                }
            });
        if (!$form->validate()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->form = $form;
    }

    public function run()
    {
        $name = Base32::decode($this->form->b32name);
        return $this->controller->render('agent', [
            'name' => $name,
            'posts' => $this->postStats,
            'combineds' => $this->getCombineds(),
        ]);
    }

    /**
     * @return array[]
     */
    public function getPostStats(): array
    {
        $list = StatAgentUser::find()
            ->andWhere(['agent' => Base32::decode($this->form->b32name)])
            ->orderBy(['date' => SORT_ASC])
            ->asArray()
            ->all();
        $ret = [];
        foreach ($list as $a) {
            $ret[$a['date']] = [
                'date'      => $a['date'],
                'battle'    => (int)$a['battle_count'],
                'user'      => (int)$a['user_count'],
            ];
        }

        // 歯抜けデータの処理
        $minDate = min(array_keys($ret));
        $maxDate = max(array_keys($ret));
        if ($minDate !== $maxDate) {
            $min = new DateTime($minDate, new DateTimeZone('Etc/GMT-6'));
            $max = new DateTime($maxDate, new DateTimeZone('Etc/GMT-6'));
            while ($min->format('U') < $max->format('U')) {
                $min->add(new DateInterval('P1D'));
                $d = $min->format('Y-m-d');
                if (!isset($ret[$d])) {
                    $ret[$d] = [
                        'date'      => $d,
                        'battle'    => 0,
                        'user'      => 0,
                    ];
                }
            }
        }
        ksort($ret);
        return array_values($ret);
    }

    /** @return AgentGroup[] */
    public function getCombineds(): array
    {
        return AgentGroup::find()
            ->innerJoinWith([
                'agentGroupMaps' => function (ActiveQuery $q): void {
                    $q->orderBy([]);
                },
            ], false)
            ->where([
                '{{agent_group_map}}.[[agent_name]]' => Base32::decode($this->form->b32name),
            ])
            ->orderBy(['{{agent_group}}.[[name]]' => SORT_ASC])
            ->all();
    }
}
