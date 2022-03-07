<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\models\Language;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\web\Cookie;
use yii\web\Response;

final class LanguageAction extends Action
{
    private ?string $previousLanguage = null;

    /** @inheritdoc */
    public function init()
    {
        $this->previousLanguage = Yii::$app->language;
        Yii::$app->language = 'en-US';
        parent::init();
    }

    public function run(): Response
    {
        $response = Yii::$app->response;
        $response->format = 'json';
        $this->runImpl($response);
        return $response;
    }

    private function runImpl(Response $response): void
    {
        $form = $this->makeValidationModel();
        $form->language = Yii::$app->request->post('language');
        if (!$form->validate()) {
            $response->statusCode = 400;
            $response->data = [
                'errors' => $form->getErrors(),
            ];
            return;
        }

        $response->cookies->add(
            new Cookie([
                'name' => 'language',
                'value' => $form->language,
                'expire' => time() + 86400 * 366,
            ]),
        );

        $response->data = [
            'previous' => $this->previousLanguage,
            'next' => $form->language,
        ];
    }

    private function makeValidationModel()
    {
        return DynamicModel::validateData(
            [
                'language' => null,
            ],
            [
                [['language'], 'required'],
                [['language'], 'exist',
                    'targetClass' => Language::class,
                    'targetAttribute' => 'lang',
                ],
            ]
        );
    }
}
