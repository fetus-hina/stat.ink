<?php
use yii\db\Migration;
use app\models\Fest;
use app\components\json\OfficialJson;

class m150803_090004_second_fest_data extends Migration
{
    protected function getFestId()
    {
        return 2;
    }

    public function safeUp()
    {
        $fest = Fest::findOne(['id' => $this->getFestId()]);
        if (!$fest) {
            return false;
        }

        foreach ($this->findAllJsonFiles() as $entry) {
            if (!$json = file_get_contents($entry->path)) {
                return false;
            }

            $parsed = new OfficialJson($fest, $json);
            $this->insert('official_data', [
                'fest_id' => $fest->id,
                'sha256sum' => $parsed->getSHA256Sum(),
                'downloaded_at' => $entry->time,
            ]);
            $id = $this->db->getLastInsertID();
            $winCount = $parsed->getWinCounts();
            $this->batchInsert('official_win_data', ['data_id', 'color_id', 'count'], [
                [
                    'data_id' => $id,
                    'color_id' => 1,
                    'count' => $winCount->alpha,
                ],
                [
                    'data_id' => $id,
                    'color_id' => 2,
                    'count' => $winCount->bravo,
                ],
            ]);
        }
    }

    public function safeDown()
    {
        // DELETE official_win_data
        // SQLite not supports DELETE with JOIN
        $inner = 'SELECT {{official_data}}.[[id]] FROM {{official_data}} WHERE {{official_data}}.[[fest_id]] = :id';
        $sql = "DELETE FROM {{official_win_data}} WHERE {{official_win_data}}.[[data_id]] IN ( {$inner} )";
        $this->execute($sql, [':id' => $this->getFestId()]);

        // DELETE official_data
        $this->delete('official_data', '[[fest_id]] = :id', [':id' => $this->getFestId()]);
    }

    private function findAllJsonFiles()
    {
        $dirPath = __DIR__ . '/../data/old-results/' . $this->getFestId();
        $ret = [];
        foreach (new \DirectoryIterator($dirPath) as $entry) {
            if (!$entry->isFile()) {
                continue;
            }
            if (!preg_match(
                '/^(\d{4}-\d{2}-\d{2})T(\d{2}-\d{2}-\d{2})\.json$/',
                $entry->getBasename(),
                $match
            )) {
                continue;
            }

            $t = strtotime($match[1] . 'T' . strtr($match[2], '-', ':') . '+09');
            $ret[] = (object)[
                'time' => $t,
                'path' => $entry->getPathname(),
            ];
        }
        usort($ret, function ($a, $b) {
            return $a->time - $b->time;
        });
        return $ret;
    }
}
