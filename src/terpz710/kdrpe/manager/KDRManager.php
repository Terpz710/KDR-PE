<?php

declare(strict_types=1);

namespace terpz710\kdrpe\manager;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use function round;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\leaderboards\KillLeaderboard;
use terpz710\kdrpe\leaderboards\DeathLeaderboard;
use terpz710\kdrpe\leaderboards\KillStreakLeaderboard;

final class KDRManager {
    use SingletonTrait;

    public Main $plugin;

    public Config $data;

    public function __construct() {
        $this->plugin = Main::getInstance();
        $this->data = new Config($this->plugin->getDataFolder() . "kdr.json", Config::JSON);
    }

    public function createKDRProfile(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();

        if (!$this->hasKDRProfile($player)) {
            $this->data->set($uuid, [
                "username" => $player->getName(),
                "kills" => 0,
                "deaths" => 0,
                "killstreak" => 0
            ]);
            KillLeaderboard::getInstance()->updateFloatingText();
            DeathLeaderboard::getInstance()->updateFloatingText();
            KillStreakLeaderboard::getInstance()->updateFloatingText();
            $this->data->save();
        }
    }

    public function hasKDRProfile(Player $player) : bool{
        return $this->data->exists($player->getUniqueId()->toString());
    }

    public function getKills(Player $player) : ?int{
        $this->data->reload();
        return $this->data->get($player->getUniqueId()->toString())["kills"];
    }

    public function addKill(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        $data = $this->data->get($uuid);

        $data["kills"] += 1;
        $this->data->set($uuid, $data);
        $this->data->save();
        $this->plugin->getKDRScoreHud()->updateTag($player);
        KillLeaderboard::getInstance()->updateFloatingText();
    }

    public function getDeaths(Player $player) : ?int{
        $this->data->reload();
        return $this->data->get($player->getUniqueId()->toString())["deaths"];
    }

    public function addDeath(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        $data = $this->data->get($uuid);

        $data["deaths"] += 1;
        $this->data->set($uuid, $data);
        $this->data->save();
        $this->plugin->getKDRScoreHud()->updateTag($player);
        DeathLeaderboard::getInstance()->updateFloatingText();
    }

    public function getKillStreak(Player $player) : ?int{
        $this->data->reload();
        return $this->data->get($player->getUniqueId()->toString())["killstreak"];
    }

    public function addKillStreak(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        $data = $this->data->get($uuid);

        $data["killstreak"] += 1;
        $this->data->set($uuid, $data);
        $this->data->save();
        $this->plugin->getKDRScoreHud()->updateTag($player);
        KillStreakLeaderboard::getInstance()->updateFloatingText();
    }

    public function resetKillStreak(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        $data = $this->data->get($uuid);

        $data["killstreak"] = 0;
        $this->data->set($uuid, $data);
        $this->data->save();
        $this->plugin->getKDRScoreHud()->updateTag($player);
        KillStreakLeaderboard::getInstance()->updateFloatingText();
    }

    public function getKDR(Player $player) : ?float{
        $kills = $this->getKills($player);
        $deaths = $this->getDeaths($player);
        $kdr = ($deaths === 0) ? $kills : round($kills / $deaths, 2);
        return $kdr;
    }

    public function updateUsername(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        if ($this->hasKDRProfile($player)) {
            $currentData = $this->data->get($uuid);
            if ($currentData["username"] !== $player->getName()) {
                $currentData["username"] = $player->getName();
                $this->data->set($uuid, $currentData);
                $this->data->save();
            }
        }
    }
}