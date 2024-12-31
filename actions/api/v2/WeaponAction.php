<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2;

use Generator;
use Yii;
use app\models\Language;
use app\models\Weapon2;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

use function array_merge;
use function sprintf;

use const SORT_ASC;

class WeaponAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $query = Weapon2::find()
            ->with([
                'canonical',
                'mainPowerUp',
                'mainReference',
                'special',
                'subweapon',
                'type',
                'type.category',
            ])
            ->orderBy('[[id]]');

        //TODO: query filter

        switch ((string)Yii::$app->request->get('format')) {
            case 'csv':
                $response->format = 'csv';
                $response->setDownloadHeaders('statink-weapon2.csv', 'text/csv; charset=UTF-8');
                return [
                    'separator' => ',',
                    'inputCharset' => Yii::$app->charset,
                    'outputCharset' => 'UTF-8',
                    'appendBOM' => true,
                    'rows' => $this->formatCsvRows(
                        $query
                            ->innerJoinWith(['type', 'type.category'])
                            ->orderBy([
                                'weapon_category2.id' => SORT_ASC,
                                'weapon_type2.id' => SORT_ASC,
                                'weapon2.name' => SORT_ASC,
                            ])
                            ->all(),
                    ),
                ];

            default:
                return ArrayHelper::getColumn(
                    $query->all(),
                    fn (Weapon2 $weapon): array => $weapon->toJsonArray(),
                );
        }
    }

    /**
     * @param Weapon2[]
     * @return Generator<string[]>
     */
    protected function formatCsvRows(array $weapons): Generator
    {
        $langs = Language::find()
            ->standard()
            ->orderBy(['lang' => SORT_ASC])
            ->asArray()
            ->select(['lang'])
            ->column();
        yield array_merge(
            ['category1', 'category2', 'key', 'subweapon', 'special', 'mainweapon', 'reskin', 'splatnet'],
            ArrayHelper::getColumn(
                $langs,
                fn (string $lang) => sprintf('[%s]', $lang),
            ),
        );

        $i18n = Yii::$app->i18n;
        foreach ($weapons as $weapon) {
            yield array_merge(
                [
                    (string)ArrayHelper::getValue($weapon, 'type.category.key'),
                    (string)ArrayHelper::getValue($weapon, 'type.key'),
                    (string)ArrayHelper::getValue($weapon, 'key'),
                    (string)ArrayHelper::getValue($weapon, 'subweapon.key'),
                    (string)ArrayHelper::getValue($weapon, 'special.key'),
                    (string)ArrayHelper::getValue($weapon, 'mainReference.key'),
                    (string)ArrayHelper::getValue($weapon, 'canonical.key'),
                    (string)ArrayHelper::getValue($weapon, 'splatnet'),
                ],
                ArrayHelper::getColumn(
                    $langs,
                    fn (string $langCode): string => $i18n->translate(
                        'app-weapon2',
                        $weapon->name,
                        [],
                        $langCode,
                    ),
                ),
            );
        }
    }
}
