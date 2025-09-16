<?php

declare(strict_types=1);

namespace terpz710\kdrpe\data;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\data\saved\KDRSavedData;

final class KDR {
    use SingletonTrait;

    protected Config $config;

    public function __construct() {
        $dataFolder = Main::getInstance()->getDataFolder();

        @mkdir($dataFolder . "database/");
        $this->config = new Config($dataFolder . "database/data.json");
    }

    public function hasAccount($player) : bool{
        if($player instanceof Player){
            $player = $player->getName();
        }

        return $this->config->exists($player);
    }

    public function createAccount($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        if(!$this->hasAccount($player)){
            $this->config->set($player, [
                "kills" => 0,
                "deaths" => 0,
                "kill_streak" => 0
            ]);
            $this->config->save();
        }
    }

    public function addKill($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $kills = KDRSavedData::getInstance()->getKills($player);

        $this->config->setNested("$player.kills", $kills + 1);
        $this->config->save();
    }

    public function addKillStreak($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $killstreak = KDRSavedData::getInstance()->getKillStreak($player);

        $this->config->setNested("$player.kill_streak", $killstreak + 1);
        $this->config->save();
    }

    public function resetKillStreak($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $this->config->setNested("$player.kill_streak", 0);
        $this->config->save();
    }

    public function addDeath($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $deaths = KDRSavedData::getInstance()->getDeaths($player);

        $this->config->setNested("$player.deaths", $deaths + 1);
        $this->config->save();
    }
}
