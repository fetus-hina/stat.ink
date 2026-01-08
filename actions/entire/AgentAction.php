<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeZone;
use ParagonIE\ConstantTime\Base32;
use RangeException;
use Yii;
use app\models\AgentGroup;
use app\models\StatAgentUser;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;

use function array_keys;
use function array_values;
use function ksort;
use function max;
use function mb_check_encoding;
use function min;

use const SORT_ASC;

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
                try {
                    $decoded = Base32::decode($form->$attr);
                    if ($decoded === '') {
                        $form->addError($attr, 'invalid name');
                        return;
                    }
                } catch (RangeException $e) {
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
        try {
            $name = Base32::decode($this->form->b32name);
        } catch (RangeException $e) {
            $name = '';
        }
        return $this->controller->render('agent', [
            'name' => $name,
            'posts' => $this->postStats,
            'combineds' => $this->combineds,
        ]);
    }

    public function getPostStats()
    {
        try {
            $list = StatAgentUser::find()
                ->andWhere(['agent' => Base32::decode($this->form->b32name)])
                ->orderBy(['date' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (RangeException $e) {
            $list = [];
        }
        $ret = [];
        foreach ($list as $a) {
            $ret[$a['date']] = [
                'date' => $a['date'],
                'battle' => (int)$a['battle_count'],
                'user' => (int)$a['user_count'],
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
                        'date' => $d,
                        'battle' => 0,
                        'user' => 0,
                    ];
                }
            }
        }
        ksort($ret);
        return array_values($ret);
    }

    public function getCombineds()
    {
        try {
            return AgentGroup::find()
                ->innerJoinWith([
                    'agentGroupMaps' => function ($q) {
                        $q->orderBy(null);
                    },
                ], false)
                ->where([
                    '{{agent_group_map}}.[[agent_name]]' => Base32::decode($this->form->b32name),
                ])
                ->orderBy(['{{agent_group}}.[[name]]' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (RangeException $e) {
            return [];
        }
    }
}
