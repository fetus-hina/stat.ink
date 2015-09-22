<?php
if (!class_exists('m150803_090004_second_fest_data', false)) {
    require_once(__DIR__ . '/m150803_090004_second_fest_data.php');
}

class m150803_090006_third_fest_data extends m150803_090004_second_fest_data
{
    protected function getFestId()
    {
        return 3;
    }
}
