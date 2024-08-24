<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use terpz710\kdrpe\Main;

class KdrManager {

    private $killStreakConfig;
    private array $killStreaks = [];
    private array $playerDataCache = [];
    private $plugin;
    private $scoreHud;
    private $dataPath;

    public function __construct() {
        $this->plugin = Main::getInstance();
        $this->dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $this->loadKillStreakData();
    }

    public function loadPlayerData(): void {
        if (file_exists($this->dataPath)) {
            $this->playerDataCache = json_decode(file_get_contents($this->dataPath), true);
        }
    }

    public function savePlayerData(): void {
        file_put_contents($this->dataPath, json_encode($this->playerDataCache, JSON_PRETTY_PRINT));
    }

    public function initializePlayerData(string $playerName): void {
        $this->scoreHud = new KdrScoreHud();
        if (!isset($this->playerDataCache[$playerName])) {
            $this->playerDataCache[$playerName] = ['kills' => 0, 'deaths' => 0];
            $this->savePlayerData();
            $this->scoreHud->updateScoreHudTags($this->plugin->getServer()->getPlayerExact($playerName));
        }
    }

    public function incrementKill(string $playerName): void {
        $this->playerDataCache[$playerName]['kills']++;
        $this->savePlayerData();
    }

    public function incrementDeath(string $playerName): void {
        $this->playerDataCache[$playerName]['deaths']++;
        $this->savePlayerData();
    }

    public function getKills(string $playerName): int {
        return $this->playerDataCache[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        return $this->playerDataCache[$playerName]['deaths'] ?? 0;
    }

    public function getPlayerData(): array {
        return $this->playerDataCache;
    }

    public function getTopKills(): array {
        $topKills = [];
        foreach ($this->playerDataCache as $playerName => $data) {
            $kills = $data['kills'] ?? 0;
            $topKills[$playerName] = $kills;
        }
        arsort($topKills);
        return array_slice($topKills, 0, 10);
    }

    private function loadKillStreakData(): void {
        $this->killStreakConfig = new Config($this->plugin->getDataFolder() . 'killstreak.json', Config::JSON, []);
        $this->killStreaks = $this->killStreakConfig->getAll();
    }

    public function saveKillStreakData(): void {
        $this->killStreakConfig->setAll($this->killStreaks);
        $this->killStreakConfig->save();
    }
}
