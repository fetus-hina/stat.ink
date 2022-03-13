<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\components\helpers\T;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2Tier;
use yii\base\Action;
use yii\web\Response;

use const SORT_ASC;
use const SORT_DESC;

final class Weapons2TierAction extends Action
{
    /** @var array<string, string> */
    public array $input;

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

    /**
     * @return Response|string
     */
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

            // @phpstan-ignore-next-line
            $latest = StatWeapon2Tier::find()
                ->thresholded()
                ->andWhere(['rule_id' => $rule->id ?? null])
                ->orderBy(['id' => SORT_DESC])
                ->limit(1)
                ->one();
            if (!$latest) {
                return T::webControllerEx($this->controller)->error404();
            }

            return T::webControllerEx($this->controller)
                ->redirect(['entire/weapons2-tier',
                    'version' => $latest->versionGroup->tag,
                    'month' => substr($latest->month, 0, 7), // "YYYY-MM"-DD
                    'rule' => 'area',
                ]);
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $this->input['month'])) {
            return T::webControllerEx($this->controller)->error404();
        }

        $rule = Rule2::find()
            ->andWhere(['key' => (string)$this->input['rule']])
            ->andWhere(['<>', 'key', 'nawabari'])
            ->limit(1)
            ->one();
        if (!$rule) {
            return T::webControllerEx($this->controller)->error404();
        }

        $vGroup = SplatoonVersionGroup2::find()
            ->andWhere(['tag' => (string)$this->input['version']])
            ->limit(1)
            ->one();
        if (!$vGroup) {
            return T::webControllerEx($this->controller)->error404();
        }

        // @phpstan-ignore-next-line
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
            return T::webControllerEx($this->controller)->error404();
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
            // @phpstan-ignore-next-line
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
