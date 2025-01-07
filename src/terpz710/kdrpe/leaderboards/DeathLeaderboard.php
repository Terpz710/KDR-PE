<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboards;

use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\kdrpe\manager\KDRManager;

use terpz710\kdrpe\floatingtext\FloatingText;

use Ramsey\Uuid\Uuid;

final class DeathLeaderboard {
    use SingletonTrait;

    public function getTopDeaths(int $limit = 10) : array{
        $kdrManager = KDRManager::getInstance();
        $allData = $kdrManager->data->getAll();
        $deaths = [];

        foreach ($allData as $uuid => $info) {
            $player = Server::getInstance()->getPlayerByUUID(Uuid::fromString($uuid));
            if ($player !== null) {
                $deaths[$info["username"]] = $kdrManager->getDeaths($player);
            } else {
                $deaths[$info["username"]] = $info["deaths"];
            }
        }

        $kdrManager->data->reload();
        arsort($deaths);
        return array_slice($deaths, 0, $limit, true);
    }

    public function updateFloatingText() : void{
        $topDeaths = $this->getTopDeaths();
        $text = "§l§c-=Top Deaths Leaderboard=-\n";

        $rank = 1;
        foreach ($topDeaths as $username => $deaths) {
            $text .= "§r§e{$rank}. {$username} - {$deaths} deaths\n";
            $rank++;
        }

        FloatingText::update("death_leaderboard", $text);
    }
}