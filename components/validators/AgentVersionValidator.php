<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\validators;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

use function is_string;
use function trim;
use function version_compare;

final class AgentVersionValidator extends Validator
{
    public string $gameVersion = 'FILLME';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = 'Your client is out-of-date. Please update your client.';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model->hasErrors()) {
            return;
        }

        $agentName = trim((string)ArrayHelper::getValue($model, 'agent'));
        $agentVersion = trim((string)ArrayHelper::getValue($model, 'agent_version'));

        $minimumVersion = match ($agentName) {
            's3s' => ArrayHelper::getValue(
                Yii::$app->params,
                ['agentRequirements', $this->gameVersion, 's3s'],
            ),
            's3si.ts' => ArrayHelper::getValue(
                Yii::$app->params,
                ['agentRequirements', $this->gameVersion, 's3sits'],
            ),
            default => null,
        };
        if (!is_string($minimumVersion)) {
            return;
        }

        if (version_compare($agentVersion, $minimumVersion, '>=')) {
            return;
        }

        Yii::warning(
            "Outdated client name={$agentName}, version={$agentVersion}, minimum={$minimumVersion}",
            __METHOD__,
        );

        $this->addError($model, $attribute, $this->message);
    }
}
