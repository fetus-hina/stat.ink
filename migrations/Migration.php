<?php
class Migration extends \yii\db\Migration
{
    public function apiKey(int $length = 16)
    {
        return $this->string($length)->notNull()->unique();
    }

    public function timestampTZ(int $precision = 0, bool $withTZ = true)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(
            sprintf('TIMESTMAP(%d) %s TIME ZONE', $precision, $withTZ ? 'WITH' : 'WITHOUT'),
            null
        );
    }
}
