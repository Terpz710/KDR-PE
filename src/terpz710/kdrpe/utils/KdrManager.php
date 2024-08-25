<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use terpz710\kdrpe\Main;

class KdrManager {

    private array $playerDataCache = [];
    private array $killStreaks = [];
    private $plugin;

    public function __construct() {
        $this->plugin = Main::getInstance();
        $this->loadKillStreakData();
        $this->loadPlayerData();
    }

    private function loadPlayerData(): void {
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        if (file_exists($dataPath)) {
            $this->playerDataCache = json_decode(file_get_contents($dataPath), true);
        }
    }

    private function savePlayerData(): void {
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        file_put_contents($dataPath, json_encode($this->playerDataCache, JSON_PRETTY_PRINT));
    }

    public function initializePlayerData(string $playerName): void {
        if (!isset($this->playerDataCache[$playerName])) {
            $this->playerDataCache[$playerName] = ['kills' => 0, 'deaths' => 0];
        }
    }

    public function initializeKillStreak(string $playerName): void {
        if (!isset($this->killStreaks[$playerName])) {
            $this->killStreaks[$playerName] = 0;
            $this->saveKillStreakData();
        }
    }


    public function incrementKill(string $playerName): void {
        $this->playerDataCache[$playerName]['kills']++;
    }

    public function incrementDeath(string $playerName): void {
        $this->playerDataCache[$playerName]['deaths']++;
    }

    public function getKills(string $playerName): int {
        return $this->playerDataCache[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        return $this->playerDataCache[$playerName]['deaths'] ?? 0;
    }

    public function getTopKills(): array {
        $topKills = [];

        foreach ($this->playerDataCache as $playerName => $data) {
            $topKills[$playerName] = $data['kills'] ?? 0;
        }

        arsort($topKills);

        return array_slice($topKills, 0, 10);
    }

    public function updateFloatingText() {
        $filePath = $this->plugin->getDataFolder() . "floating_text_data.json";
        $text = $this->getFloatingText();
        FloatingText::update($text, $filePath);
    }

    public function getFloatingText(): string {
        $topKillData = $this->getTopKills();

        $text = "-----------§eTOP KILLS§f-----------\n";

        $rank = 1;
        foreach ($topKillData as $playerName => $kills) {
            $text .= "§e{$rank}. §f{$playerName}: §e{$kills}\n";
            $rank++;

            if ($rank > 10) {
                break;
            }
        }

        return $text;
    }

    public function getKillStreak(string $playerName): int {
        return $this->killStreaks[$playerName] ?? 0;
    }

    public function resetKillStreak(string $playerName): void {
        unset($this->killStreaks[$playerName]);
    }

    public function handleKillStreak(string $playerName, int $killStreak): void {
        if ($killStreak >= 5) {
            $this->plugin->getServer()->broadcastMessage("{$playerName} is on a {$killStreak}-kill streak!");
        }
    }

    private function loadKillStreakData(): void {
        $config = new Config($this->plugin->getDataFolder() . 'killstreak.json', Config::JSON, []);
        $this->killStreaks = $config->getAll();
    }

    public function saveKillStreakData(): void {
        $config = new Config($this->plugin->getDataFolder() . 'killstreak.json', Config::JSON);
        $config->setAll($this->killStreaks);
        $config->save();
    }

    public function saveAllData(): void {
        $this->savePlayerData();
        $this->saveKillStreakData();
    }
}
