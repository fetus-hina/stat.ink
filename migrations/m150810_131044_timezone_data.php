<?php
use yii\db\Schema;
use yii\db\Migration;

class m150810_131044_timezone_data extends Migration
{
    public function safeUp()
    {
        if (!$list = $this->getTimezoneList()) {
            return false;
        }

        foreach ($list as $row) {
            $this->insert('timezone', [
                'zone' => $row,
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('timezone');
    }

    private function getTimezoneList()
    {
        $ret = [];
        if (!$fh = @fopen(__DIR__ . '/../runtime/tzdata/zone.tab', 'rt')) {
            return false;
        }
        while (!feof($fh)) {
            $line = trim(fgets($fh));
            if ($line == '' || substr($line, 0, 1) === '#') {
                continue;
            }
            
            if (preg_match('!\b[A-Z][a-zA-Z_-]+/[A-Z][a-zA-Z_-]+(?:/[A-Z][a-zA-Z_-]+)?\b!', $line, $match)) {
                $ret[] = $match[0];
            }
        }
        fclose($fh);

        $ret[] = 'Etc/UTC';

        natcasesort($ret);
        return array_unique($ret);
    }
}
