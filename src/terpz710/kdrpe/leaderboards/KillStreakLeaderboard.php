<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboards;

use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\kdrpe\manager\KDRManager;

use terpz710\kdrpe\floatingtext\FloatingText;

use Ramsey\Uuid\Uuid;

final class KillStreakLeaderboard {
    use SingletonTrait;

    public function getTopKillStreak(int $limit = 10) : array{
        $kdrManager = KDRManager::getInstance();
        $allData = $kdrManager->data->getAll();
        $killstreak = [];

        foreach ($allData as $uuid => $info) {
            $player = Server::getInstance()->getPlayerByUUID(Uuid::fromString($uuid));
            if ($player !== null) {
                $killstreak[$info["username"]] = $kdrManager->getKillStreak($player);
            } else {
                $killstreak[$info["username"]] = $info["killstreak"];
            }
        }

        $kdrManager->data->reload();
        arsort($killstreak);
        return array_slice($killstreak, 0, $limit, true);
    }

    public function updateFloatingText() : void{
        $topKillStreak = $this->getTopKillStreak();
        $text = "§l§a-=Top KillStreak Leaderboard=-\n";

        $rank = 1;
        foreach ($topKillStreak as $username => $killstreak) {
            $text .= "§r§e{$rank}. {$username} - {$killstreak} killstreak\n";
            $rank++;
        }

        FloatingText::update("killstreak_leaderboard", $text);
    }
}