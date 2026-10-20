<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeZone;
use Override;
use ParagonIE\ConstantTime\Base32;
use RangeException;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\AgentGroup;
use app\models\EntireCombinedAgentForm;
use app\models\StatAgentUser;
use yii\base\Action;
use yii\db\Query;
use yii\web\NotFoundHttpException;

use function array_keys;
use function array_map;
use function array_values;
use function ksort;
use function max;
use function min;
use function sprintf;

use const SORT_ASC;

/**
 * @property-read list<array{date: string, battle: int, user: int}> $postStats
 */
final class CombinedAgentAction extends Action
{
    public ?EntireCombinedAgentForm $form = null;
    public AgentGroup $agentGroup;

    #[Override]
    public function init(): void
    {
        parent::init();

        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        $form = new EntireCombinedAgentForm(['b32name' => Yii::$app->request->get('b32name')]);
        if (!$form->validate()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->agentGroup = TypeHelper::instanceOf($form->getAgentGroup(), AgentGroup::class);
        $this->form = $form;
    }

    public function run(): string
    {
        try {
            $name = Base32::decode(TypeHelper::string($this->form?->b32name));
        } catch (RangeException $e) {
            $name = '';
        }

        return $this->controller->render('combined-agent', [
            'group' => $this->agentGroup,
            'name' => $name,
            'posts' => $this->postStats,
        ]);
    }

    /**
     * @return list<array{date: string, battle: int, user: int}>
     */
    public function getPostStats(): array
    {
        $query = new Query()
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
        $minDate = $ret ? ($ret |> array_keys(...) |> min(...)) : '1970-01-01';
        $maxDate = $ret ? ($ret |> array_keys(...) |> max(...)) : '1970-01-01';
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
