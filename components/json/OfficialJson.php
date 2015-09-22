<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\json;

use Yii;
use yii\base\Component;
use app\models\Fest;

class OfficialJson extends Component
{
    private $fest;
    private $json;
    private $sha256sum;

    public function __construct(Fest $fest, $jsonText)
    {
        $this->fest = $fest;
        $this->json = json_decode($jsonText, true);
        $this->sha256sum = base64_encode(hash('sha256', $jsonText, true));
    }

    public function getSHA256Sum()
    {
        return $this->sha256sum;
    }

    public function getWinCounts()
    {
        $aTeam = $this->fest->alphaTeam;
        $bTeam = $this->fest->bravoTeam;
        $aCount = 0;
        $bCount = 0;
        foreach ($this->json as $battle) {
            $winTeamName = $battle['win_team_name'];
            if (strpos($winTeamName, $aTeam->keyword) !== false) {
                ++$aCount;
            }
            if (strpos($winTeamName, $bTeam->keyword) !== false) {
                ++$bCount;
            }
        }
        return (object)[
            'alpha' => $aCount,
            'bravo' => $bCount,
        ];
    }

    public function getMvpList()
    {
        $aTeam = $this->fest->alphaTeam;
        $bTeam = $this->fest->bravoTeam;
        foreach ($this->json as $battle) {
            $winTeamName = $battle['win_team_name'];
            if (strpos($winTeamName, $aTeam->keyword) !== false) {
                yield array_merge(
                    $battle,
                    ['x_win_team_side' => 'alpha']
                );
            } elseif (strpos($winTeamName, $bTeam->keyword) !== false) {
                yield array_merge(
                    $battle,
                    ['x_win_team_side' => 'bravo']
                );
            }
        }
    }
}
