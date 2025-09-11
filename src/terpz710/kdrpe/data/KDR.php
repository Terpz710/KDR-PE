<?php

declare(strict_types=1);

namespace terpz710\kdrpe\data;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\event\AddKillEvent;
use terpz710\kdrpe\event\AddKillStreakEvent;
use terpz710\kdrpe\event\ResetKillStreakEvent;
use terpz710\kdrpe\event\AddDeathEvent;

final class KDR {
    use SingletonTrait;

    protected Config $config;

    public function __construct() {
        $dataFolder = Main::getInstance()->getDataFolder();

        @mkdir($dataFolder . "database/");
        $this->config = new Config($dataFolder . "database/data.json");
    }

    public function hasAccount($player) : bool{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;

        return $this->config->exists($player);
    }

    public function createAccount($player) : void{
        if ($player instanceof Player) {
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
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;

        if (!$this->hasAccount($player)) {
            return;
        }

        $data = $this->config->get($player);

        $e = new AddKillEvent($player);
        $e->call();

        $data["kills"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function addKillStreak($player) : void{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;

        if (!$this->hasAccount($player)) {
            return;
        }

        $data = $this->config->get($player);

        $e = new AddKillStreakEvent($player);
        $e->call();

        $data["kill_streak"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function resetKillStreak($player) : void{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;

        if (!$this->hasAccount($player)) {
            return;
        }

        $data = $this->config->get($player);

        $e = new ResetKillStreakEvent($player);
        $e->call();

        $data["kill_streak"] = 0;
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function addDeath($player) : void{
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = $player;

        if (!$this->hasAccount($player)) {
            return;
        }

        $data = $this->config->get($player);

        $e = new AddDeathEvent($player);
        $e->call();

        $data["deaths"] += 1;
        $this->config->set($player, $data);
        $this->config->save();
    }
}
