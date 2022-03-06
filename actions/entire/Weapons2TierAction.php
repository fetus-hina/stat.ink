<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2Tier;
use yii\web\ViewAction;

use const SORT_ASC;
use const SORT_DESC;

class Weapons2TierAction extends ViewAction
{
    public $input;

    public function init()
    {
        parent::init();

        $request = Yii::$app->request;
        $this->input = [
            'version' => (string)$request->get('version'),
            'month' => (string)$request->get('month'),
            'rule' => (string)$request->get('rule'),
        ];
    }

    public function run()
    {
        // redirect to default page if input is empty
        if (!$this->input['version'] || !$this->input['month'] || !$this->input['rule']) {
            $rule = null;
            foreach ([$this->input['rule'], 'area'] as $ruleKey) {
                if ($ruleKey) {
                    if ($rule = Rule2::findOne(['key' => (string)$ruleKey])) {
                        break;
                    }
                }
            }

            $latest = StatWeapon2Tier::find()
                ->thresholded()
                ->andWhere(['rule_id' => $rule->id ?? null])
                ->orderBy(['id' => SORT_DESC])
                ->limit(1)
                ->one();
            if (!$latest) {
                $this->controller->error404();
                return;
            }

            $this->controller->redirect(['entire/weapons2-tier',
                'version' => $latest->versionGroup->tag,
                'month' => substr($latest->month, 0, 7), // "YYYY-MM"-DD
                'rule' => 'area',
            ]);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $this->input['month'])) {
            $this->controller->error404();
            return;
        }

        $rule = Rule2::find()
            ->andWhere(['key' => (string)$this->input['rule']])
            ->andWhere(['<>', 'key', 'nawabari'])
            ->limit(1)
            ->one();
        if (!$rule) {
            $this->controller->error404();
            return;
        }

        $vGroup = SplatoonVersionGroup2::find()
            ->andWhere(['tag' => (string)$this->input['version']])
            ->limit(1)
            ->one();
        if (!$vGroup) {
            $this->controller->error404();
            return;
        }

        $data = StatWeapon2Tier::find()
            ->thresholded()
            ->andWhere([
                'version_group_id' => $vGroup->id,
                'month' => $this->input['month'] . '-01',
                'rule_id' => $rule->id,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->with([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ])
            ->all();
        if (!$data) {
            $this->controller->error404();
            return;
        }

        return $this->controller->render('weapons2-tier', [
            'data' => $data,
            'versionGroup' => $vGroup,
            'month' => $this->input['month'],
            'rule' => $rule,
            'rules' => $this->getRules($vGroup),
            'versions' => StatWeapon2Tier::getDateVersionPatterns($rule),
        ]);
    }

    private function getRules(SplatoonVersionGroup2 $version): array
    {
        return Rule2::getSortedAll('gachi', null, fn (Rule2 $rule): array => [
            'name' => $rule->name,
            'enabled' => StatWeapon2Tier::find()
                    ->thresholded()
                    ->andWhere([
                        'version_group_id' => $version->id,
                        'month' => $this->input['month'] . '-01',
                        'rule_id' => $rule->id,
                    ])
                    ->limit(1)
                    ->exists(),
        ]);
    }
}
