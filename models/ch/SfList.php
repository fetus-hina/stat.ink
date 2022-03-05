<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\ch;

use Yii;
use yii\base\Model;

class SfList extends Model
{
    public $items = [];

    public static function create(string $text, bool $sort = true): ?self
    {
        $obj = Yii::createObject(['__class' => static::class]);
        if (!$obj->parse($text, $sort)) {
            return null;
        }
        return $obj;
    }

    public function __toString()
    {
        return implode(',', array_map(
            fn (SfItem $item): string => (string)$item,
            $this->items
        ));
    }

    protected function parse(string $encoded, bool $sort): bool
    {
        $this->items = [];
        $items = [];

        while (strlen($encoded) > 0) {
            if (!$_ = SfItem::parseAndCreate($encoded)) {
                return false;
            }

            if (!$_[0]) {
                return false;
            }
            $items[] = $_[0];

            if ($_[1] === '') { // done!
                break;
            }

            if (!preg_match('/\A[\x20\x09]*,[\x20\x09]*(.*)\z/', $_[1], $match)) {
                return false;
            }
            $encoded = $match[1];
        }

        if ($sort) {
            usort($items, [SfItem::class, 'compare']);
        }

        $this->items = array_values($items);
        return true;
    }
}
