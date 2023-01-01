<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use DateTime;
use DateTimeImmutable;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\User;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Response;

final class ActivityAction extends Action
{
    /** @var Response */
    public $resp;

    public function init()
    {
        parent::init();

        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->db->setTimezone('Etc/UTC');
        $this->resp = Yii::$app->response;
        $this->resp->format = Response::FORMAT_JSON;
    }

    public function run()
    {
        $form = $this->getInputPseudoForm();
        if ($form->hasErrors()) {
            $this->resp->statusCode = 400;
            $this->resp->data = $form->getErrors();
            return;
        }

        $user = User::findOne(['screen_name' => $form->screen_name]);
        [$from, $to] = BattleHelper::getActivityDisplayRange();
        $this->resp->data = $this->makeData($user, $from, $to, $form->only);
    }

    private function getInputPseudoForm(): DynamicModel
    {
        $req = Yii::$app->getRequest();
        $time = time();
        return DynamicModel::validateData(
            [
                'screen_name' => $req->get('screen_name'),
                'only' => $req->get('only'),
            ],
            [
                [['screen_name'], 'required'],
                [['screen_name', 'only'], 'string'],
                [['screen_name'], 'exist', 'skipOnError' => true,
                    'targetClass' => User::class,
                    'targetAttribute' => 'screen_name',
                ],
                [['only'], 'in', 'range' => ['spl1', 'spl2', 'spl3', 'salmon2', 'salmon3']],
            ],
        );
    }

    private function makeData(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?string $only
    ): array {
        return $this->reformatData($this->mergeData([
            $this->isShow('spl1', $only)
                ? $this->makeDataSplatoon1Battle($user, $from, $to)
                : [],
            $this->isShow('spl2', $only)
                ? $this->makeDataSplatoon2Battle($user, $from, $to)
                : [],
            $this->isShow('salmon2', $only)
                ? $this->makeDataSplatoon2Salmon($user, $from, $to)
                : [],
            $this->isShow('spl3', $only)
                ? $this->makeDataSplatoon3Battle($user, $from, $to)
                : [],
            $this->isShow('salmon3', $only)
                ? $this->makeDataSplatoon3Salmon($user, $from, $to)
                : [],
        ]));
    }

    private function isShow(string $kind, ?string $only): bool
    {
        if ($only === null) {
            return true;
        }

        return $kind === $only;
    }

    private function reformatData(array $inData): array
    {
        return array_map(
            fn (string $date, int $count): array => [
                    'date' => $date,
                    'count' => $count,
                ],
            array_keys($inData),
            array_values($inData),
        );
    }

    private function mergeData(array $dataList): array
    {
        $result = [];
        foreach ($dataList as $data) {
            foreach ($data as $date => $count) {
                $result[$date] = ($result[$date] ?? 0) + (int)$count;
            }
        }
        ksort($result, SORT_STRING);
        return $result;
    }

    private function makeDataSplatoon1Battle(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{battle}}.[[start_at]] IS NOT NULL THEN {{battle}}.[[start_at]]',
            "WHEN {{battle}}.[[end_at]] IS NOT NULL THEN {{battle}}.[[end_at]] - '3 minutes'::interval",
            "ELSE {{battle}}.[[at]] - '4 minutes'::interval",
        ]));
        $query = (new Query())
            ->select([
                'date' => $date,
                'count' => 'COUNT(*)',
            ])
            ->from('battle')
            ->andWhere(['user_id' => $user->id])
            ->andWhere([
                'between',
                $date,
                $from->format(DateTime::ATOM),
                $to->format(DateTime::ATOM),
            ])
            ->groupBy([$date])
            ->orderBy(['date' => SORT_ASC]);
        return $this->listToMap($query->all());
    }

    private function makeDataSplatoon2Battle(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
            "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
            "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
        ]));
        $query = (new Query())
            ->select([
                'date' => $date,
                'count' => 'COUNT(*)',
            ])
            ->from('battle2')
            ->andWhere(['user_id' => $user->id])
            ->andWhere([
                'between',
                $date,
                $from->format(DateTime::ATOM),
                $to->format(DateTime::ATOM),
            ])
            ->groupBy([$date])
            ->orderBy(['date' => SORT_ASC]);
        return $this->listToMap($query->all());
    }

    private function makeDataSplatoon2Salmon(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{salmon2}}.[[start_at]] IS NOT NULL THEN {{salmon2}}.[[start_at]]',
            "ELSE {{salmon2}}.[[created_at]] - '5 minutes'::interval",
        ]));
        $query = (new Query())
            ->select([
                'date' => $date,
                'count' => 'COUNT(*)',
            ])
            ->from('salmon2')
            ->andWhere(['user_id' => $user->id])
            ->andWhere([
                'between',
                $date,
                $from->format(DateTime::ATOM),
                $to->format(DateTime::ATOM),
            ])
            ->groupBy([$date])
            ->orderBy(['date' => SORT_ASC]);
        return $this->listToMap($query->all());
    }

    private function makeDataSplatoon3Battle(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{%battle3}}.[[start_at]] IS NOT NULL THEN {{%battle3}}.[[start_at]]',
            "WHEN {{%battle3}}.[[end_at]] IS NOT NULL THEN {{%battle3}}.[[end_at]] - '3 minutes'::interval",
            "ELSE {{%battle3}}.[[created_at]] - '4 minutes'::interval",
        ]));
        $query = (new Query())
            ->select([
                'date' => $date,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->andWhere([
                'between',
                $date,
                $from->format(DateTime::ATOM),
                $to->format(DateTime::ATOM),
            ])
            ->groupBy([$date])
            ->orderBy(['date' => SORT_ASC]);
        return $this->listToMap($query->all());
    }

    private function makeDataSplatoon3Salmon(
        User $user,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{%salmon3}}.[[start_at]] IS NOT NULL THEN {{%salmon3}}.[[start_at]]',
            'ELSE {{%salmon3}}.[[created_at]]',
        ]));
        $query = (new Query())
            ->select([
                'date' => $date,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->andWhere([
                'between',
                $date,
                $from->format(DateTime::ATOM),
                $to->format(DateTime::ATOM),
            ])
            ->groupBy([$date])
            ->orderBy(['date' => SORT_ASC]);
        return $this->listToMap($query->all());
    }

    private function listToMap(array $list): array
    {
        return ArrayHelper::map($list, 'date', 'count');
    }
}
