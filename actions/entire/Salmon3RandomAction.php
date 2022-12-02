<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\SalmonSchedule3;
use app\models\SalmonWeapon3;
use yii\base\Action;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

use const SORT_DESC;

final class Salmon3RandomAction extends Action
{
    public function run()
    {
        return Yii::$app->db->transaction(
            function (): string {
                // FIXME
                $schedule = SalmonSchedule3::find()
                    ->andWhere(['start_at' => '2022-12-02T17:00:00+09:00'])
                    ->limit(1)
                    ->one();

                if (!$schedule) {
                    throw new ServerErrorHttpException();
                }

                $counts = $this->getWeaponCount($schedule);

                return $this->controller->render('salmon3-random', [
                    'counts' => $counts,
                    'max' => \max(\array_values($counts)),
                    'total' => \array_sum(\array_values($counts)),
                    'weapons' => ArrayHelper::map(
                        SalmonWeapon3::find()->all(),
                        'key',
                        fn (SalmonWeapon3 $model): SalmonWeapon3 => $model,
                    ),
                ]);
            },
            Transaction::READ_COMMITTED,
        );
    }

    /**
     * @return array<string, int>
     */
    private function getWeaponCount(SalmonSchedule3 $schedule): array
    {
        $query = (new Query())
            ->select([
                'key' => '{{%salmon_weapon3}}.[[key]]',
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_player3}}', '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]')
            ->innerJoin('{{%salmon_player_weapon3}}', '{{%salmon_player3}}.[[id]] = {{%salmon_player_weapon3}}.[[player_id]]')
            ->innerJoin('{{%salmon_weapon3}}', '{{%salmon_player_weapon3}}.[[weapon_id]] = {{%salmon_weapon3}}.[[id]]')
            ->andWhere([
                '{{%salmon3}}.[[has_broken_data]]' => false,
                '{{%salmon3}}.[[has_disconnect]]' => false,
                '{{%salmon3}}.[[is_automated]]' => true,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
            ])
            ->andWhere(['and',
                ['>=', '{{%salmon3}}.[[start_at]]', $schedule->start_at],
                ['<', '{{%salmon3}}.[[start_at]]', $schedule->end_at],
            ])
            ->groupBy(['{{%salmon_weapon3}}.[[key]]'])
            ->orderBy([
                'COUNT(*)' => SORT_DESC,
                '{{%salmon_weapon3}}.[[key]]' => SORT_ASC,
            ]);
        return ArrayHelper::map(
            $query->all(),
            'key',
            fn (array $row): int => (int)$row['count'],
        );
    }
}
