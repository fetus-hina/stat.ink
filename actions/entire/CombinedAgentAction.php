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
use yii\db\Query;
use yii\web\NotFoundHttpException;

use function array_keys;
use function array_map;
use function array_values;
use function ksort;
use function max;
use function mb_check_encoding;
use function min;
use function sprintf;

use const SORT_ASC;

final class CombinedAgentAction extends Action
{
    public $form;
    public $agentGroup;

    public function init()
    {
        parent::init();

        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        $action = $this;
        $form = new DynamicModel(['b32name' => Yii::$app->request->get('b32name')]);
        $form->addRule('b32name', 'required')
            ->addRule('b32name', 'match', ['pattern' => '/^[a-zA-Z2-7]+$/'])
            ->addRule('b32name', function ($attr, $conf) use ($action, $form) {
                try {
                    $decoded = Base32::decode($form->$attr);
                } catch (RangeException $e) {
                    $decoded = '';
                }
                if ($decoded === '') {
                    $form->addError($attr, 'invalid name');
                    return;
                }

                if (!mb_check_encoding($decoded, 'UTF-8')) {
                    $form->addError($attr, 'broken encoding');
                    return;
                }

                $group = AgentGroup::findOne(['name' => $decoded]);
                if (!$group) {
                    $form->addError($attr, 'not found');
                    return;
                }

                $action->agentGroup = $group;
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

        return $this->controller->render('combined-agent', [
            'group' => $this->agentGroup,
            'name' => $name,
            'posts' => $this->postStats,
        ]);
    }

    public function getPostStats()
    {
        $query = (new Query())
            ->select([
                'date' => '{{t}}.[[date]]',
                'battle_count' => 'SUM({{t}}.[[battle_count]])',
                'user_count' => 'SUM({{t}}.[[user_count]])',
            ])
            ->from(sprintf('%s t', StatAgentUser::tableName()))
            ->where([
                '{{t}}.[[agent]]' => array_map(
                    fn ($a) => $a['agent_name'],
                    $this->agentGroup->getAgentGroupMaps()->asArray()->all(),
                ),
            ])
            ->groupBy('{{t}}.[[date]]')
            ->orderBy(['[[date]]' => SORT_ASC]);

        $ret = [];
        foreach ($query->all() as $a) {
            $ret[$a['date']] = [
                'date' => $a['date'],
                'battle' => (int)$a['battle_count'],
                'user' => (int)$a['user_count'],
            ];
        }

        // 歯抜けデータの処理
        $minDate = $ret ? min(array_keys($ret)) : '1970-01-01';
        $maxDate = $ret ? max(array_keys($ret)) : '1970-01-01';
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
}
