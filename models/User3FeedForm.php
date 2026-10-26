<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class User3FeedForm extends Model
{
    use ValidateDataTrait;

    /**
     * @var string|null
     */
    public $lang;
    /**
     * @var string|null
     */
    public $screen_name;
    /**
     * @var string|null
     */
    public $type;

    #[Override]
    public function rules()
    {
        return [
            [['lang', 'screen_name', 'type'], 'required'],
            [['lang'], 'exist',
                'targetClass' => Language::class,
                'targetAttribute' => 'lang',
            ],
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
            [['type'], 'in', 'range' => ['atom', 'rss']],
        ];
    }
}
