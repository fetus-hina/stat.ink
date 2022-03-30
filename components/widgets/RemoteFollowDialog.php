<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\components\helpers\Html;
use app\models\RemoteFollowModalForm;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;

class RemoteFollowDialog extends Widget
{
    public $user;

    public function run()
    {
        return Html::tag('div', $this->renderDialog(), [
            'aria-labelledby' => $this->id . '-label',
            'class' => 'modal fade',
            'id' => $this->id,
            'role' => 'dialog',
            'tabindex' => '-1',
        ]);
    }

    private function renderDialog(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    $this->renderHeader(),
                    $this->renderBody(),
                ]),
                ['class' => 'modal-content']
            ),
            [
                'class' => 'modal-dialog',
                'role' => 'document',
            ]
        );
    }

    private function renderHeader(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderCloseButton(),
                $this->renderTitle(),
            ]),
            ['class' => 'modal-header']
        );
    }

    private function renderCloseButton(): string
    {
        return Html::button(
            Html::tag(
                'span',
                (string)FA::fas('times')->fw(),
                ['aria-hidden' => 'true']
            ),
            [
                'aria-label' => Yii::t('app', 'Close'),
                'class' => 'close',
                'data-dismiss' => 'modal',
            ]
        );
    }

    private function renderTitle(): string
    {
        return Html::tag(
            'h4',
            vsprintf('%s %s (%s)', [
                Html::img('@web/static-assets/ostatus/ostatus.min.svg', [
                    'style' => [
                        'height' => '1em',
                        'width' => 'auto',
                        'vertical-align' => 'baseline',
                    ],
                ]),
                Html::encode(Yii::t('app', 'Remote Follow')),
                Html::encode(vsprintf('@%s@%s', [
                    $this->user->screen_name,
                    Yii::$app->request->hostName,
                ])),
            ]),
            [
                'class' => 'modal-title',
                'id' => $this->id . '-label',
            ]
        );
    }

    private function renderBody(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderDescription(),
                Html::tag('hr'),
                Html::tag('div', $this->renderForm(), ['class' => 'mt-3']),
            ]),
            ['class' => 'modal-body']
        );
    }

    private function renderDescription(): string
    {
        //FIXME: i18n
        return implode('', [
            Html::tag(
                'p',
                Html::encode(
                    'マストドンなどのOStatus対応サービスを利用して、' .
                    'バトル結果を購読することができます。'
                )
            ),
            Html::tag(
                'p',
                implode('<br>', [
                    Html::encode(sprintf(
                        'このユーザ（@%s@%s）をフォローする、あなたのアカウント名を' .
                        '「ユーザ名@サーバ」の形式で入力してください。',
                        (string)$this->user->screen_name,
                        (string)Yii::$app->request->hostName,
                    )),
                    sprintf(
                        '例えば、mstdn.jpの利用者であれば「%s」、Pawooの利用者であれば「%s」です。',
                        Html::tag('code', Html::encode('your_id@mstdn.jp')),
                        Html::tag('code', Html::encode('your_id@pawoo.net')),
                    ),
                ])
            ),
        ]);
    }

    private function renderForm(): string
    {
        $form = RemoteFollowModalForm::factory();
        ob_start();
        try {
            $_ = ActiveForm::begin([
                'action' => ['/ostatus/start-remote-follow',
                    'screen_name' => $this->user->screen_name,
                ],
            ]);
            echo (string)$_->field($form, 'screen_name')
                ->hiddenInput(['value' => $this->user->screen_name])
                ->label(false);
            echo (string)$_->field($form, 'account')
                ->textInput(['placeholder' => '例: your_id@mstdn.jp'])
                ->label('あなたのアカウント');
            echo Html::tag(
                'div',
                Html::submitButton(
                    Html::encode('指定アカウントでこのユーザをフォローする'),
                    ['class' => 'btn btn-primary btn-block']
                ),
                ['class' => 'form-group']
            );
            ActiveForm::end();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
