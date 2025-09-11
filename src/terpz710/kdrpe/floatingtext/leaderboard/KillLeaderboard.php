<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboard;

use terpz710\kdrpe\data\saved\KDRSavesData;

use terpz710\kdrpe\floatingtext\FloatingText;

final class KillLeaderboard {

    public static function getTopKillers(int $limit = 10) : array{
        $accounts = KDRSavesData::getInstance()->getAllAccounts();

        $kills = [];
        foreach ($accounts as $name => $stats) {
            $kills[$name] = $stats["kills"];
        }

        arsort($kills);

        return array_slice($kills, 0, $limit, true);
    }

    public function updateKillFT() : void{
        $top_killers = $this->getTopKillers();
        $text = "§l§a-=Top Kills Leaderboard=-\n";

        $rank = 1;
        foreach ($top_killers as $name => $kills) {
            $text .= "§r§e{$rank}. {$name} - {$kills} kills\n";
            $rank++;
        }

        FloatingText::update("kill_leaderboard", $text);
    }
}