<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\api\internal;

use RuntimeException;
use yii\base\Model;

use function preg_last_error_msg;
use function preg_replace;
use function trim;

class PatchBattleForm extends Model
{
    /**
     * @var string|array<mixed>|null
     */
    public $link_url;
    /**
     * @var string|array<mixed>|null
     */
    public $note;
    /**
     * @var string|array<mixed>|null
     */
    public $private_note;

    public function rules()
    {
        return [
            [['link_url'], 'url'],
            [['note', 'private_note'], 'string'],
            [['note', 'private_note'], 'filter',
                'filter' => function ($value) {
                    $value = (string)$value;
                    $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value)
                        ?? throw new RuntimeException(preg_last_error_msg());
                    $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value)
                        ?? throw new RuntimeException(preg_last_error_msg());
                    $value = trim($value);
                    return $value === '' ? null : $value;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
