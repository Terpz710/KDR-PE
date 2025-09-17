<?php

declare(strict_types=1);

namespace terpz710\kdrpe\data\saved;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\data\KDR;

final class KDRSavedData {
    use SingletonTrait;

    public function getAllAccounts() : array{
        return KDR::getInstance()->getConfig()->getAll();
    }

    public function getKills($player) : int{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $data = KDR::getInstance()->getConfig()->get($player);
        return (int)($data["kills"] ?? 0);
    }

    public function getDeaths($player) : int {
        if($player instanceof Player){
            $player = $player->getName();
        }

        $data = KDR::getInstance()->getConfig()->get($player);
        return (int)($data["deaths"] ?? 0);
    }

    public function getKillStreak($player) : int{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $data = KDR::getInstance()->getConfig()->get($player);
        return (int)($data["kill_streak"] ?? 0);
    }

    public function getKDR($player) : float{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $kills = $this->getKills($player);
        $deaths = $this->getDeaths($player);

        return $deaths === 0 ? (float)$kills : round($kills / $deaths, 2);
    }
}
