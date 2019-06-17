<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2Tier;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\ViewAction;

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
            $latest = StatWeapon2Tier::find()
                ->thresholded()
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
            'prev' => $this->getPrevious($vGroup, $rule),
            'next' => $this->getNext($vGroup, $rule),
            'latest' => $this->getLatest($vGroup, $rule),
        ]);
    }

    private function getRules(SplatoonVersionGroup2 $version): array
    {
        return Rule2::getSortedAll('gachi', null, function (Rule2 $rule) use ($version): array {
            return [
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
            ];
        });
    }

    private function getPrevious(SplatoonVersionGroup2 $curVersion, Rule2 $rule): ?string
    {
        $model = StatWeapon2Tier::find()
            ->thresholded()
            ->andWhere(['and',
                ['rule_id' => $rule->id],
                ['<=', 'version_group_id', $curVersion->id],
                ['<', 'month', $this->input['month'] . '-01'],
            ])
            ->orderBy([
                'month' => SORT_DESC,
                'version_group_id' => SORT_DESC,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            return null;
        }

        return Url::to(['entire/weapons2-tier',
            'version' => $model->versionGroup->tag,
            'month' => substr($model->month, 0, 7),
            'rule' => $rule->key,
        ]);
    }

    private function getNext(SplatoonVersionGroup2 $curVersion, Rule2 $rule): ?string
    {
        $model = StatWeapon2Tier::find()
            ->thresholded()
            ->andWhere(['and',
                ['rule_id' => $rule->id],
                ['or',
                    ['and',
                        ['=', 'month', $this->input['month'] . '-01'],
                        ['>', 'version_group_id', $curVersion->id],
                    ],
                    ['and',
                        ['>', 'month', $this->input['month'] . '-01'],
                        ['>=', 'version_group_id', $curVersion->id],
                    ],
                ],
            ])
            ->orderBy([
                'month' => SORT_ASC,
                'version_group_id' => SORT_ASC,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            return null;
        }

        return Url::to(['entire/weapons2-tier',
            'version' => $model->versionGroup->tag,
            'month' => substr($model->month, 0, 7),
            'rule' => $rule->key,
        ]);
        return null;
    }

    private function getLatest(SplatoonVersionGroup2 $curVersion, Rule2 $rule): ?string
    {
        $model = StatWeapon2Tier::find()
            ->thresholded()
            ->andWhere(['rule_id' => $rule->id])
            ->orderBy([
                'month' => SORT_DESC,
                'version_group_id' => SORT_DESC,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            return null;
        }

        if ($model->version_group_id > $curVersion->id ||
            $model->month > $this->input['month'] . '-01'
        ) {
            return Url::to(['entire/weapons2-tier',
                'version' => $model->versionGroup->tag,
                'month' => substr($model->month, 0, 7),
                'rule' => $rule->key,
            ]);
        }

        return null;
    }
}
