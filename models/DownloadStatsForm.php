<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class DownloadStatsForm extends Model
{
    use ValidateDataTrait;

    public $lang;
    public $charset;
    public $bom;
    public $tsv;

    #[Override]
    public function rules()
    {
        return [
            [['lang', 'charset'], 'string'],
            [['bom', 'tsv'], 'boolean'],
            [['lang'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => 'lang',
            ],
            [['charset'], 'exist', 'skipOnError' => true,
                'targetClass' => Charset::class,
                'targetAttribute' => ['charset' => 'php_name'],
            ],
        ];
    }
}
