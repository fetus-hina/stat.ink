<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\internal;

use yii\base\Model;

use function preg_replace;
use function trim;

class PatchBattleForm extends Model
{
    public $link_url;
    public $note;
    public $private_note;

    public function rules()
    {
        return [
            [['link_url'], 'url'],
            [['note', 'private_note'], 'string'],
            [['note', 'private_note'], 'filter',
                'filter' => function ($value) {
                    $value = (string)$value;
                    $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value);
                    $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value);
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
