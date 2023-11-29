<?php

declare(strict_types=1);

namespace Terpz710\KDRPE;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;

use Terpz710\KDRPE\Command\KDRCommand;
use Terpz710\KDRPE\Command\TopKillCommand;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getCommandMap()->register('kdr', new KDRCommand($this));
        $this->getServer()->getCommandMap()->register('topkill', new TopKillCommand($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if (!file_exists($this->getDataFolder() . 'data.yml')) {
            $this->saveResource('data.yml');
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $this->initializePlayerData($player->getName());

        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            if ($damager instanceof Player) {
                $this->incrementKill($damager->getName());
            }
        }

        $this->incrementDeath($player->getName());
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        $this->initializePlayerData($playerName);
    }

    private function initializePlayerData(string $playerName): void {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        if (!$config->exists($playerName . '.kills') || !$config->exists($playerName . '.deaths')) {
            $config->set($playerName . '.kills', 0);
            $config->set($playerName . '.deaths', 0);
            $config->save();
        }
    }

    private function incrementKill(string $playerName): void {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        $kills = $config->get($playerName . '.kills', 0);
        $config->set($playerName . '.kills', ++$kills);
        $config->save();
    }

    private function incrementDeath(string $playerName): void {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        $deaths = $config->get($playerName . '.deaths', 0);
        $config->set($playerName . '.deaths', ++$deaths);
        $config->save();
    }

    public function getPlayerData(): array {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        return $config->getAll();
    }

    public function getKills(string $playerName): int {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        return $config->get($playerName . '.kills', 0);
    }

    public function getDeaths(string $playerName): int {
        $config = new Config($this->getDataFolder() . 'data.yml', Config::YAML);
        return $config->get($playerName . '.deaths', 0);
    }

    public function getTopKills(): array {
        $playerData = $this->getPlayerData();
        arsort($playerData);
        return array_slice($playerData, 0, 5);
    }
}
