<?php
use yii\db\Migration;
use app\models\Fest;
use app\models\OfficialData;
use app\models\Mvp;

class m150916_123919_mvp_data extends Migration
{
    public function safeUp()
    {
        foreach (range(2, 5) as $festId) {
            if (!$this->upFest($festId)) {
                return false;
            }
        }
    }

    public function safeDown()
    {
    }

    private function upFest($festId)
    {
        echo "Processing fest {$festId}\n";
        if (!$fest = Fest::findOne(['id' => $festId])) {
            echo "unknown fest {$festId}\n";
            return false;
        }
        if (!$alpha = $fest->alphaTeam) {
            echo "alpha team not found\n";
            return false;
        }
        if (!$bravo = $fest->bravoTeam) {
            echo "bravo team not found\n";
            return false;
        }
        if (!$files = $this->findJsonFiles($festId)) {
            echo "JSON files not exist\n";
            return false;
        }
        foreach ($files as $file) {
            $officialData = OfficialData::findOne([
                'fest_id' => $fest->id,
                'downloaded_at' => $file->time,
            ]);
            if (!$officialData) {
                echo "official_data not exists. {$fest->id}, {$file->time}\n";
                return false;
            }
            $json = json_decode(file_get_contents($file->path, false, null));
            $batch = [];
            foreach ($json as $data) {
                if (strpos($data->win_team_name, $alpha->keyword) !== false) {
                    $color = 1;
                } elseif (strpos($data->win_team_name, $bravo->keyword) !== false) {
                    $color = 2;
                } else {
                    echo "Unknown MVP Team {$data->win_team_name}";
                    return false;
                }
                $batch[] = [
                    'data_id' => $officialData->id,
                    'color_id' => $color,
                    'name' => $data->win_team_mvp,
                ];
            }
            $this->batchInsert('mvp', ['data_id', 'color_id', 'name'], $batch);
        }
        return true;
    }

    private function findJsonFiles($festId)
    {
        $dirName = Yii::getAlias('@app/data/old-results') . '/' . $festId;
        $ret = [];
        foreach (new DirectoryIterator($dirName) as $entry) {
            if ($entry->isFile() && $entry->getExtension() === 'json') {
                if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2})-(\d{2})-(\d{2})/', $entry->getFileName(), $match)) {
                    $ret[] = (object)[
                        'time' => mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]),
                        'path' => $entry->getPathName(),
                    ];
                }
            }
        }
        usort($ret, function ($a, $b) {
            return $a->time - $b->time;
        });
        return $ret;
    }
}
