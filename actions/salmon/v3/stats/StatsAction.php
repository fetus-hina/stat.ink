<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
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
use app\actions\salmon\v3\stats\schedule\SpecialTrait;
use app\actions\salmon\v3\stats\stats\PlayerTrait;
use app\actions\salmon\v3\stats\stats\WeaponTrait;
use app\components\helpers\TypeHelper;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_merge;
use function is_string;

final class StatsAction extends Action
{
    use AbstractTrait;
    use BossSalmonidTrait;
    use EventTrait;
    use KingSalmonidTrait;
    use PlayerTrait;
    use SpecialTrait;
    use WeaponTrait;

    public ?User $user = null;

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

        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string
    {
        if (!($user = $this->user)) {
            throw new LogicException();
        }

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => Yii::$app->cache->getOrSet(
                [
                    'id' => __METHOD__,
                    'version' => 3,
                    'user' => $user->id,
                    'cond' => $this->getCachingCondition($db, $user),
                ],
                fn (): array => [
                    'bossStats' => $this->getBossStats($db, $user, null),
                    'bosses' => $this->getBosses($db),
                    'eventStats' => $this->getEventStats($db, $user, null),
                    'events' => $this->getEvents($db),
                    'kingStats' => $this->getKingStats($db, $user, null),
                    'kings' => $this->getKings($db),
                    'playerStats' => $this->getPlayerStats($db, $user),
                    'specialStats' => $this->getSpecialStats($db, $user, null),
                    'specials' => $this->getSpecials($db),
                    'stats' => $this->getStats($db, $user, null),
                    'tides' => $this->getTides($db),
                    'weaponStats' => $this->getWeaponStats($db, $user),
                    'weapons' => $this->getWeapons($db),
                ],
                duration: 7 * 24 * 60 * 60,
            ),
            Transaction::REPEATABLE_READ,
        );

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render(
                'stats/stats',
                array_merge(
                    $data,
                    [
                        'user' => $user,
                    ],
                ),
            );
    }

    private function getCachingCondition(Connection $db, User $user): array
    {
        return (new Query())
            ->select([
                'max' => 'MAX([[id]])',
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere([
                'is_deleted' => false,
                'is_eggstra_work' => false,
                'is_private' => false,
                'user_id' => $user->id,
            ])
            ->one($db);
    }
}
