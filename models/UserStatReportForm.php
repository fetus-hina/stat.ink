<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\base\Model;

final class UserStatReportForm extends Model
{
    use ValidateDataTrait;

    /**
     * @var int|string|null
     */
    public $year;
    /**
     * @var int|string|null
     */
    public $month;

    private int $maxYear = 0;

    /**
     * The latest year that can be requested (not a form attribute)
     */
    public function setMaxYear(int $maxYear): void
    {
        $this->maxYear = $maxYear;
    }

    #[Override]
    public function rules()
    {
        return [
            [['year'], 'required'],
            [['year'], 'integer',
                'min' => 2015,
                'max' => $this->maxYear,
            ],
            [['month'], 'integer',
                'min' => 1,
                'max' => 12,
            ],
        ];
    }
}
