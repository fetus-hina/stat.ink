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

    /** @var string|array<mixed>|null */
    public $lang;
    /** @var string|array<mixed>|null */
    public $charset;
    /** @var string|array<mixed>|null */
    public $bom;
    /** @var string|array<mixed>|null */
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
