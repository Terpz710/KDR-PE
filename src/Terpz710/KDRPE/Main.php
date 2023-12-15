<?php

declare(strict_types=1);

namespace Terpz710\KDRPE;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\utils\Config;

use Terpz710\KDRPE\Command\KDRCommand;
use Terpz710\KDRPE\Command\SeeKDRCommand;
use Terpz710\KDRPE\Command\TopKillCommand;
use Terpz710\KDRPE\Command\TopKillFTCommand;
use Terpz710\KDRPE\API\FloatingKDRAPI;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getCommandMap()->register('kdr', new KDRCommand($this));
        $this->getServer()->getCommandMap()->register('seekdr', new SeeKDRCommand($this));
        $this->getServer()->getCommandMap()->register('topkill', new TopKillCommand($this));
        $this->getServer()->getCommandMap()->register('topkillfloatingtext', new TopKillFTCommand($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $kdrFolderPath = $this->getDataFolder() . 'KDR';
        if (!is_dir($kdrFolderPath)) {
            @mkdir($kdrFolderPath);
        }

        $dataPath = $kdrFolderPath . DIRECTORY_SEPARATOR . 'data.json';
        if (!file_exists($dataPath)) {
            $initialData = [];
            file_put_contents($dataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }

        $ftFolderPath = $this->getDataFolder() . 'FT';
        if (!is_dir($ftFolderPath)) {
            @mkdir($ftFolderPath);
        }

        $ftDataPath = $ftFolderPath . DIRECTORY_SEPARATOR . 'floating_text_data.json';
        if (!file_exists($ftDataPath)) {
            $initialData = [];
            file_put_contents($ftDataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }
        if (file_exists($ftDataPath)) {
            $ftData = json_decode(file_get_contents($ftDataPath), true);
            foreach ($ftData as $tag => $positionData) {
                $position = new Position($positionData['x'], $positionData['y'], $positionData['z'], $this->getServer()->getWorldManager()->getWorldByName($positionData['world']));
                FloatingKDRAPI::create($position, $tag, '', $this->getDataFolder());
            }
        }
    }

    public function onDisable(): void {
        $ftFolderPath = $this->getDataFolder() . 'FT';
        $ftDataPath = $ftFolderPath . DIRECTORY_SEPARATOR . 'floating_text_data.json';
        FloatingKDRAPI::saveToFile($ftFolderPath . DIRECTORY_SEPARATOR . 'floating_text_data.json');
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
        $ftFolderPath = $this->getDataFolder() . 'FT';
        FloatingKDRAPI::loadFromFile($ftFolderPath . DIRECTORY_SEPARATOR . 'floating_text_data.json', $ftFolderPath);
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $ftFolderPath = $this->getDataFolder() . 'FT';
        FloatingKDRAPI::saveToFile($ftFolderPath . DIRECTORY_SEPARATOR . 'floating_text_data.json');
    }

    private function initializePlayerData(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';

        $playerData = json_decode(file_get_contents($dataPath), true);

        if (!isset($playerData[$playerName])) {
            $playerData[$playerName] = ['kills' => 0, 'deaths' => 0];
            file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
        }
    }

    private function incrementKill(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['kills']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
    }

    private function incrementDeath(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['deaths']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
    }

    public function getPlayerData(): array {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        return $playerData ?? [];
    }

    public function getKills(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['deaths'] ?? 0;
    }

    public function getTopKills(): array {
        $playerData = $this->getPlayerData();
        $topKills = [];

        foreach ($playerData as $playerName => $data) {
            $kills = $data['kills'] ?? 0;
            $topKills[$playerName] = $kills;
        }

        arsort($topKills);

        return array_slice($topKills, 0, 5);
    }
}
