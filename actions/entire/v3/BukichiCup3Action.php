<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use LogicException;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Event3;
use app\models\Lobby3;
use app\models\Weapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function implode;
use function sprintf;

use const SORT_ASC;

final class BukichiCup3Action extends Action
{
    public function run(): string
    {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $event = Event3::find()
            ->andWhere(['internal_id' => 'TGVhZ3VlTWF0Y2hFdmVudC1SYW5kb21XZWFwb24='])
            ->limit(1)
            ->one();
        if (!$event) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        // TODO: schedule filter
        return $controller->render(
            'v3/bukichi-cup3',
            Yii::$app->db->transaction(
                fn (Connection $db): array => [
                    'data' => self::makeData($db, $event),
                    'event' => $event,
                    'weapons' => self::getWeapons($db), // TODO: filter by release_at with schedule
                ],
                Transaction::REPEATABLE_READ,
            ),
        );
    }

    private static function makeData(Connection $db, Event3 $event): array
    {
        $lobby = Lobby3::find()
            ->andWhere(['key' => 'event'])
            ->limit(1)
            ->cache(86400)
            ->one($db);
        if (!$lobby) {
            throw new LogicException();
        }

        return ArrayHelper::index(
            (new Query())
                ->select([
                    'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
                    'players' => 'COUNT(*)',
                    'player_for_winpct' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            'WHEN {{%battle_player3}}.[[is_me]] = FALSE THEN 1',
                            'WHEN {{%result3}}.[[aggregatable]] = FALSE THEN 0',
                            'ELSE 0',
                        ]),
                    ),
                    'wins' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            'WHEN {{%battle_player3}}.[[is_me]] <> FALSE THEN 0',
                            'WHEN {{%result3}}.[[aggregatable]] = FALSE THEN 0',
                            'WHEN {{%battle_player3}}.[[is_our_team]] = {{%result3}}.[[is_win]] THEN 1',
                            'ELSE 0',
                        ]),
                    ),
                ])
                ->from('{{%battle3}}')
                ->innerJoin(
                    '{{%battle_player3}}',
                    '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]',
                )
                ->innerJoin(
                    '{{%result3}}',
                    '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]',
                )
                ->andWhere(['and',
                    [
                        '{{%battle3}}.[[event_id]]' => $event->id,
                        '{{%battle3}}.[[has_disconnect]]' => false,
                        '{{%battle3}}.[[is_automated]]' => true,
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                        '{{%battle3}}.[[use_for_entire]]' => true,
                    ],
                    ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                ])
                ->groupBy([
                    '{{%battle_player3}}.[[weapon_id]]',
                ])
                ->all($db),
            'weapon_id',
        );
    }

    private static function getWeapons(Connection $db): array
    {
        return ArrayHelper::index(
            Weapon3::find()
                ->joinWith('weapon3Aliases', false)
                ->andWhere(['~', '{{%weapon3_alias}}.[[key]]', '^\d+$'])
                ->orderBy([
                    'CAST({{%weapon3_alias}}.[[key]] AS INTEGER)' => SORT_ASC,
                ])
                ->all($db),
            'id',
        );
    }
}
