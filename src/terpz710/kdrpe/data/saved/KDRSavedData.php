<?php

declare(strict_types=1);

namespace terpz710\kdrpe\data\saved;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\Main;

final class KDRSavedData {
    use SingletonTrait;

    protected Config $config;

    public function __construct() {
        $dataFolder = Main::getInstance()->getDataFolder();

        @mkdir($dataFolder . "database/");
        $this->config = new Config($dataFolder . "database/data.json");
    }

    public function getAllAccounts() : array{
        return $this->config->getAll();
    }

    public function getKills($player) : int{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $data = $this->config->get($player);
        if ($data === false) {
            return 0;
        }

        return (int) $data["kills"];
    }

    public function getDeaths($player) : int{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $data = $this->config->get($player);
        if ($data === false) {
            return 0;
        }

        return (int) $data["deaths"];
    }

    public function getKillStreak($player) : int{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $data = $this->config->get($player);
        
        if ($data === false) {
            return 0;
        }

        return (int) $data["kill_streak"];
    }

    public function getKDR($player) : ?float{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;
        
        $kills = $this->getKills($player);
        $deaths = $this->getDeaths($player);
        $kdr = ($deaths === 0) ? $kills : round($kills / $deaths, 2);
        return $kdr;
    }
}
