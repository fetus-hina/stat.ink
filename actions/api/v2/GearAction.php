<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v2;

use Yii;
use app\models\Gear2;
use app\models\Language;
use app\models\api\v2\GearGetForm;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function array_merge;
use function preg_match;
use function sprintf;

use const SORT_ASC;

class GearAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = new GearGetForm();
        $form->attributes = Yii::$app->getRequest()->get();
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $query = Gear2::find()
            ->innerJoinWith([
                'type',
                'brand',
            ])
            ->with([
                'ability',
                'brand.strength',
                'brand.weakness',
            ])
            ->orderBy([
                '{{gear2}}.[[type_id]]' => SORT_ASC,
                '{{gear2}}.[[key]]' => SORT_ASC,
            ]);
        $form->filterQuery($query);

        switch ((string)Yii::$app->request->get('format')) {
            case 'csv':
                $response->format = 'csv';
                return $this->formatCsv($query);

            default:
                return $this->formatJson($query);
        }
    }

    protected function formatJson(Query $query): array
    {
        return array_map(
            fn (Gear2 $gear): array => $gear->toJsonArray(),
            $query->all(),
        );
    }

    protected function formatCsv(Query $query): array
    {
        $resp = Yii::$app->response;

        $type = Yii::$app->request->get('type');
        $resp->setDownloadHeaders(
            preg_match('/^[a-z]+$/', (string)$type)
                ? "statink-gear2-{$type}.csv"
                : 'statink-gear2.csv',
            'text/csv; charset=UTF-8',
        );
        return [
            'separator' => ',',
            'inputCharset' => Yii::$app->charset,
            'outputCharset' => 'UTF-8',
            'appendBOM' => true,
            'rows' => $this->formatCsvRows($query),
        ];
    }

    protected function formatCsvRows(Query $query)
    {
        $langs = Language::find()
            ->standard()
            ->orderBy(['lang' => SORT_ASC])
            ->asArray()
            ->select(['lang'])
            ->column();
        yield array_merge(
            ['type', 'brand', 'key', 'splatnet', 'primary_ability'],
            array_map(
                fn (string $lang): string => sprintf('[%s]', $lang),
                $langs,
            ),
        );
        $i18n = Yii::$app->i18n;
        foreach ($query->all() as $gear) {
            yield array_merge(
                [
                    $gear->type->key,
                    $gear->brand->key,
                    $gear->key,
                    $gear->splatnet,
                    $gear->ability->key ?? '',
                ],
                array_map(
                    fn (string $lang) => $i18n->translate('app-gear2', $gear->name, [], $lang),
                    $langs,
                ),
            );
        }
    }
}
