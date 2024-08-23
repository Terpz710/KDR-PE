<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\player\Player;
use pocketmine\utils\Config;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\utils\KdrScoreHud;

class KdrManager {

    private $killStreakConfig;
    private array $killStreaks = [];
    private $plugin;
    private $scoreHud;

    public function __construct() {
        $this->plugin = Main::getInstance();
        $this->loadKillStreakData();
    }

    public function initializePlayerData(string $playerName): void {
        $this->scoreHud = new KdrScoreHud();
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        if (!isset($playerData[$playerName])) {
            $playerData[$playerName] = ['kills' => 0, 'deaths' => 0];
            file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
            $this->scoreHud->updateScoreHudTags($this->plugin->getServer()->getPlayerExact($playerName));
        }
    }

    public function incrementKill(string $playerName): void {
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['kills']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
    }

    public function incrementDeath(string $playerName): void {
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['deaths']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
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

    public function getKills(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['deaths'] ?? 0;
    }

    public function getPlayerData(): array {
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        return $playerData ?? [];
    }

    public function getTopKills(): array {
        $playerData = $this->getPlayerData();
        $topKills = [];

        foreach ($playerData as $playerName => $data) {
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

    public function saveKillStreakData(): void {
        $this->killStreakConfig->setAll($this->killStreaks);
        $this->killStreakConfig->save();
    }
}
