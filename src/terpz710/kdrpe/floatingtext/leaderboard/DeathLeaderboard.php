<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboard;

use terpz710\kdrpe\data\saved\KDRSavesData;

use terpz710\kdrpe\floatingtext\FloatingText;

final class DeathLeaderboard {

    public static function getTopDeaths(int $limit = 10) : array{
        $accounts = KDRSavesData::getInstance()->getAllAccounts();

        $deaths = [];
        foreach ($accounts as $name => $stats) {
            $deaths[$name] = $stats["deaths"];
        }

        arsort($deaths);

        return array_slice($deaths, 0, $limit, true);
    }

    public function updateDeathFT() : void{
        $top_deaths = $this->getTopDeaths();
        $text = "§l§a-=Top Deaths Leaderboard=-\n";

        $rank = 1;
        foreach ($top_deaths as $name => $deaths) {
            $text .= "§r§e{$rank}. {$name} - {$deaths} deaths\n";
            $rank++;
        }

        FloatingText::update("death_leaderboard", $text);
    }
}