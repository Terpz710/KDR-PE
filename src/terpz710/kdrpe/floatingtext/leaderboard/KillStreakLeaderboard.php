<?php

declare(strict_types=1);

namespace terpz710\kdrpe\floatingtext\leaderboard;

use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\FloatingText;

final class KillStreakLeaderboard {
    use SingletonTrait;

    public function getTopKillStreak(int $limit = 10) : array{
        $accounts = KDRSavedData::getInstance()->getAllAccounts();

        $killstreak = [];
        foreach ($accounts as $name => $stats) {
            $killstreak[$name] = $stats["kill_streak"];
        }

        arsort($killstreak);

        return array_slice($killstreak, 0, $limit, true);
    }

    public function updateKillStreakFT() : void{
        $top_killstreak = $this->getTopKillStreak();
        $text = "§l§a-=Top KillStreak Leaderboard=-\n";

        $rank = 1;
        foreach ($top_killstreak as $name => $killstreak) {
            $text .= "§r§e{$rank}. {$name} - {$killstreak} killstreak\n";
            $rank++;
        }

        FloatingText::update("killstreak_leaderboard", $text);
    }
}
