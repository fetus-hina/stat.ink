<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Override;
use ParagonIE\ConstantTime\Base32;
use RangeException;
use yii\base\Model;

use function mb_check_encoding;

final class EntireCombinedAgentForm extends Model
{
    public $b32name;

    private ?AgentGroup $agentGroup = null;

    #[Override]
    public function rules()
    {
        return [
            ['b32name', 'required'],
            ['b32name', 'match', 'pattern' => '/^[a-zA-Z2-7]+$/'],
            ['b32name', function ($attr, $conf) {
                try {
                    $decoded = Base32::decode($this->$attr);
                } catch (RangeException $e) {
                    $decoded = '';
                }
                if ($decoded === '') {
                    $this->addError($attr, 'invalid name');
                    return;
                }

                if (!mb_check_encoding($decoded, 'UTF-8')) {
                    $this->addError($attr, 'broken encoding');
                    return;
                }

                $group = AgentGroup::findOne(['name' => $decoded]);
                if (!$group) {
                    $this->addError($attr, 'not found');
                    return;
                }

                $this->agentGroup = $group;
            },
            ],
        ];
    }

    /**
     * The agent group found while validating b32name
     */
    public function getAgentGroup(): ?AgentGroup
    {
        return $this->agentGroup;
    }
}
