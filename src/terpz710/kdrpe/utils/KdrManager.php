<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\player\Player;
use pocketmine\utils\Config;

use terpz710\kdrpe\Main;

class KdrManager {

    private array $playerData = [];
    private array $killStreaks = [];
    private $playerDataConfig;
    private $killStreakConfig;
    private $plugin;

    public function __construct() {
        $this->plugin = Main::getInstance();

        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $this->playerDataConfig = new Config($dataPath, Config::JSON, []);
        $this->playerData = $this->playerDataConfig->getAll();

        $this->killStreakConfig = new Config($this->plugin->getDataFolder() . 'killstreak.json', Config::JSON, []);
        $this->killStreaks = $this->killStreakConfig->getAll();
    }

    public function initializePlayerData(string $playerName): void {
        if (!isset($this->playerData[$playerName])) {
            $this->playerData[$playerName] = ['kills' => 0, 'deaths' => 0];
        }
    }

    public function incrementKill(string $playerName): void {
        $this->playerData[$playerName]['kills']++;
    }

    public function incrementDeath(string $playerName): void {
        $this->playerData[$playerName]['deaths']++;
    }

    public function getKills(string $playerName): int {
        return $this->playerData[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        return $this->playerData[$playerName]['deaths'] ?? 0;
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

    public function saveAllData(): void {
        $this->playerDataConfig->setAll($this->playerData);
        $this->playerDataConfig->save();
        $this->killStreakConfig->setAll($this->killStreaks);
        $this->killStreakConfig->save();
    }
}
