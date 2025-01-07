<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboards;

use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\kdrpe\manager\KDRManager;

use terpz710\kdrpe\floatingtext\FloatingText;

use Ramsey\Uuid\Uuid;

final class KillLeaderboard {
    use SingletonTrait;

    public function getTopKillers(int $limit = 10) : array{
        $kdrManager = KDRManager::getInstance();
        $allData = $kdrManager->data->getAll();
        $kills = [];

        foreach ($allData as $uuid => $info) {
            $player = Server::getInstance()->getPlayerByUUID(Uuid::fromString($uuid));
            if ($player !== null) {
                $kills[$info["username"]] = $kdrManager->getKills($player);
            } else {
                $kills[$info["username"]] = $info["kills"];
            }
        }

        $kdrManager->data->reload();
        arsort($kills);
        return array_slice($kills, 0, $limit, true);
    }

    public function updateFloatingText() : void{
        $topKillers = $this->getTopKillers();
        $text = "§l§a-=Top Kills Leaderboard=-\n";

        $rank = 1;
        foreach ($topKillers as $username => $kills) {
            $text .= "§r§e{$rank}. {$username} - {$kills} kills\n";
            $rank++;
        }

        FloatingText::update("kill_leaderboard", $text);
    }
}