<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Language;
use app\models\MedalCanonical3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function strnatcasecmp;

final class Medal3Action extends Action
{
    public function run(): string
    {
        $sysLang = Yii::$app->language;
        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => [
                'medals' => ArrayHelper::sort(
                    MedalCanonical3::find()->all(),
                    fn (MedalCanonical3 $a, MedalCanonical3 $b): int => $a->gold !== $b->gold
                        ? ($a->gold ? -1 : 1)
                        : (
                          strnatcasecmp(Yii::t('app-medal3', $a->name), Yii::t('app-medal3', $b->name))
                            ?: strnatcasecmp($a->name, $b->name)
                        ),
                ),
                'langs' => ArrayHelper::sort(
                    Language::find()->standard()->all(),
                    fn (Language $a, Language $b): int => match (true) {
                        $a->lang === $sysLang => - 1,
                        $b->lang === $sysLang => 1,
                        default => strnatcasecmp($a->name, $b->name),
                    },
                ),
            ],
            Transaction::REPEATABLE_READ,
        );

        return $this->controller->render('medal3', $data);
    }
}
