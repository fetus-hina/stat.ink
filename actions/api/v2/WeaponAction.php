<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2;

use Yii;
use app\models\Language;
use app\models\Weapon2;
use yii\db\ActiveQuery;
use yii\web\ViewAction as BaseAction;

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
                return $this->formatCsv($query);

            default:
                return $this->formatJson($query);
        }
    }

    protected function formatJson(ActiveQuery $query): array
    {
        return array_map(
            fn (Weapon2 $weapon): array => $weapon->toJsonArray(),
            $query->all()
        );
    }

    protected function formatCsv(ActiveQuery $query): array
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-weapon2.csv', 'text/csv; charset=UTF-8');
        return [
            'separator'     => ',',
            'inputCharset'  => Yii::$app->charset,
            'outputCharset' => 'UTF-8',
            'appendBOM'     => true,
            'rows'          => $this->formatCsvRows($query),
        ];
    }

    protected function formatCsvRows(ActiveQuery $query)
    {
        $langs = Language::find()
            ->standard()
            ->orderBy(['lang' => SORT_ASC])
            ->asArray()
            ->select(['lang'])
            ->column();
        yield array_merge(
            ['category1', 'category2', 'key', 'subweapon', 'special', 'mainweapon', 'reskin', 'splatnet'],
            array_map(
                fn (string $lang): string => sprintf('[%s]', $lang),
                $langs
            )
        );
        $query
            ->innerJoinWith([
                'type',
                'type.category',
            ])
            ->orderBy([
                'weapon_category2.id' => SORT_ASC,
                'weapon_type2.id' => SORT_ASC,
                'weapon2.name' => SORT_ASC,
            ]);
        $i18n = Yii::$app->i18n;
        foreach ($query->all() as $weapon) {
            yield array_merge(
                [
                    $weapon->type->category->key,
                    $weapon->type->key,
                    $weapon->key,
                    $weapon->subweapon->key,
                    $weapon->special->key,
                    $weapon->mainReference->key,
                    $weapon->canonical->key,
                    $weapon->splatnet,
                ],
                array_map(
                    fn (string $lang) => $i18n->translate('app-weapon2', $weapon->name, [], $lang),
                    $langs
                )
            );
        }
    }
}
