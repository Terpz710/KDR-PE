<?php

declare(strict_types=1);

namespace terpz710\kdrpe\data;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\Main;

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

        $player = $player;

        return $this->config->exists($player);
    }

    public function createAccount($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $player = $player;

        if (!$this->hasAccount($player)) {
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

        $player = $player;

        if(!$this->hasAccount($player)){
            return;
        }

        $data = $this->config->get($player);

        $data["kills"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function addKillStreak($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $player = $player;

        if(!$this->hasAccount($player)){
            return;
        }

        $data = $this->config->get($player);

        $data["kill_streak"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function resetKillStreak($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $player = $player;

        if(!$this->hasAccount($player)){
            return;
        }

        $data = $this->config->get($player);

        $data["kill_streak"] = 0;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function addDeath($player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }

        $player = $player;

        if(!$this->hasAccount($player)){
            return;
        }

        $data = $this->config->get($player);

        $data["deaths"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }
}
