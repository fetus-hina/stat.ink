<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats;

use LogicException;
use Yii;
use app\actions\salmon\v3\stats\schedule\AbstractTrait;
use app\actions\salmon\v3\stats\schedule\BossSalmonidTrait;
use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\actions\salmon\v3\stats\schedule\KingSalmonidTrait;
use app\actions\salmon\v3\stats\schedule\OverfishingTrait;
use app\actions\salmon\v3\stats\schedule\PlayerTrait;
use app\actions\salmon\v3\stats\schedule\SpecialTrait;
use app\actions\salmon\v3\stats\schedule\VersionTrait;
use app\actions\salmon\v3\stats\schedule\WeaponTrait;
use app\components\helpers\TypeHelper;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_merge;
use function is_int;
use function is_string;

final class ScheduleAction extends Action
{
    use AbstractTrait;
    use BossSalmonidTrait;
    use EventTrait;
    use KingSalmonidTrait;
    use OverfishingTrait;
    use PlayerTrait;
    use SpecialTrait;
    use VersionTrait;
    use WeaponTrait;

    public ?User $user = null;
    public ?SalmonSchedule3 $schedule = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $screenName = Yii::$app->request->get('screen_name');
        $this->user = is_string($screenName)
            ? User::find()
                ->andWhere(['screen_name' => $screenName])
                ->limit(1)
                ->one()
            : null;

        $scheduleId = TypeHelper::intOrNull(Yii::$app->request->get('schedule'));
        $this->schedule = is_int($scheduleId)
            ? SalmonSchedule3::find()
                ->andWhere(['id' => $scheduleId])
                ->with([
                    'salmonScheduleWeapon3s',
                    'salmonScheduleWeapon3s.random',
                    'salmonScheduleWeapon3s.weapon',
                ])
                ->limit(1)
                ->one()
            : null;

        if (!$this->user || !$this->schedule) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string
    {
        if (
            !($user = $this->user) ||
            !($schedule = $this->schedule)
        ) {
            throw new LogicException();
        }

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => Yii::$app->cache->getOrSet(
                [
                    'cond' => $this->getCachingCondition($db, $user, $schedule),
                    'id' => __METHOD__,
                    'revision' => ArrayHelper::getValue(Yii::$app->params, 'gitRevision.longHash'),
                    'schedule' => $schedule->id,
                    'user' => $user->id,
                    'version' => 13,
                ],
                fn (): array => [
                    'bossStats' => $this->getBossStats($db, $user, $schedule),
                    'bosses' => $this->getBosses($db),
                    'eventStats' => $this->getEventStats($db, $user, $schedule),
                    'events' => $this->getEvents($db),
                    'isRandomWeapon' => $this->isRandomWeaponSchedule($db, $schedule),
                    'kingStats' => $this->getKingStats($db, $user, $schedule),
                    'kings' => $this->getKings($db),
                    'map' => $schedule->map ?? $schedule->bigMap ?? null,
                    'overfishing' => $this->getOverfishingStats($db, $user, $schedule),
                    'playerStats' => $this->getPlayerStats($db, $user, $schedule),
                    'specialStats' => $this->getSpecialStats($db, $user, $schedule),
                    'specials' => $this->getSpecials($db),
                    'stats' => $this->getStats($db, $user, $schedule),
                    'tides' => $this->getTides($db),
                    'version' => $this->getVersion($db, $schedule),
                    'weaponStats' => $this->getWeaponStats($db, $user, $schedule),
                    'weapons' => $this->getWeapons($db),
                ],
                duration: 7 * 24 * 60 * 60,
            ),
            Transaction::REPEATABLE_READ,
        );

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render(
                'stats/schedule',
                array_merge(
                    $data,
                    [
                        'user' => $user,
                        'schedule' => $schedule,
                    ],
                ),
            );
    }

    private function getCachingCondition(
        Connection $db,
        User $user,
        SalmonSchedule3 $schedule,
    ): array {
        return (new Query())
            ->select([
                'max' => 'MAX([[id]])',
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere([
                'is_deleted' => false,
                'is_private' => false,
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
            ])
            ->one($db);
    }
}
