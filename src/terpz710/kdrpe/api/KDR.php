<?php

declare(strict_types=1);

namespace terpz710\kdrpe\api;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\database\Database;

use terpz710\kdrpe\utils\Message;

use terpz710\kdrpe\event\AddKillEvent;
use terpz710\kdrpe\event\AddKillstreakEvent;
use terpz710\kdrpe\event\ResetKillstreakEvent;
use terpz710\kdrpe\event\AddDeathEvent;

final class KDR {
    use SingletonTrait;
    
    private function __construct() {
        //NADA
    }
    
    public function getKills(Player|string $player) : ?int{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT kills FROM stats WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            $data = $result->fetchArray(SQLITE3_ASSOC);
            
            $result->finalize();
            
            return $data === false ? null : (int) $data["kills"];
        } finally {
            $stmt->close();
        }
    }
    
    public function addKill(Player|string $player, int $amount = 1) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $e = new AddKillEvent($player);
        $stmt = Database::getInstance()->getSQL()->prepare("UPDATE stats SET kills = kills + :amount WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            $result->finalize();
        } finally {
            $stmt->close();
            $e->call();
            
            if ($e->isCancelled()) {
                return;
            }
        }
    }
    
    public function getKillstreak(Player|string $player) : ?int{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT killstreak FROM stats WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            $data = $result->fetchArray(SQLITE3_ASSOC);
            
            $result->finalize();
            
            return $data === false ? null : (int) $data["killstreak"];
        } finally {
            $stmt->close();
        }
    }
    
    public function addKillstreak(Player|string $player, int $amount = 1) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $e = new AddKillstreakEvent($player);
        $stmt = Database::getInstance()->getSQL()->prepare("UPDATE stats SET killstreak = killstreak + :amount WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            $result->finalize();
        } finally {
            $stmt->close();
            $e->call();
            
            if ($e->isCancelled()) {
                return;
            }
        }
    }
    
    public function resetKillstreak(Player|string $player, int $amount = 0) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $e = new ResetKillstreakEvent($player);
        $stmt = Database::getInstance()->getSQL()->prepare("UPDATE stats SET killstreak = :amount WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            $result->finalize();
        } finally {
            $stmt->close();
            $e->call();
            
            if ($e->isCancelled()) {
                return;
            }
        }
    }
    
    public function getDeaths(Player|string $player) : ?int{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT deaths FROM stats WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            $data = $result->fetchArray(SQLITE3_ASSOC);
            
            $result->finalize();
            
            return $data === false ? null : (int) $data["deaths"];
        } finally {
            $stmt->close();
        }
    }
    
    public function addDeath(Player|string $player, int $amount = 1) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $e = new AddDeathEvent($player);
        $stmt = Database::getInstance()->getSQL()->prepare("UPDATE stats SET deaths = deaths + :amount WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            $result->finalize();
        } finally {
            $stmt->close();
            $e->call();
            
            if ($e->isCancelled()) {
                return;
            }
        }
    }
    
    public function getKDR(Player|string $player) : float{
        $player = $player instanceof Player ? $player->getName() : $player;
        $kills = $this->getKills($player);
        $deaths = $this->getDeaths($player);
        
        if ($deaths <= 0) {
            return 0.0;
        }
        
        return $kills / $deaths;
    }
    
    public function fetchStats(Player $player, bool $other = false, Player|string|null $target = null) : void{
        if ($other) {
            $name = $target instanceof Player ? $target->getName() : $target;
            $other_kills = $this->getKills($name);
            $other_killstreak = $this->getKillstreak($name);
            $other_deaths = $this->getDeaths($name);
            $other_kdr = $this->getKDR($name);
            
            $player->sendMessage((string) new Message("stats-title-other", "{player}", $name));
            $player->sendMessage((string) new Message("stats-body-kills-other", "{kills}", number_format($other_kills)));
            $player->sendMessage((string) new Message("stats-body-killstreak-other", "{killstreak}", number_format($other_killstreak)));
            $player->sendMessage((string) new Message("stats-body-deaths-other", "{deaths}", number_format($other_deaths)));
            $player->sendMessage((string) new Message("stats-body-kdr-other", "{kdr}", $other_kdr));
        } else {
            $kills = $this->getKills($player);
            $killstreak = $this->getKillstreak($player);
            $deaths = $this->getDeaths($player);
            $kdr = $this->getKDR($player);
            
            $player->sendMessage((string) new Message("stats-title", "{player}", $player->getName()));
            $player->sendMessage((string) new Message("stats-body-kills", "{kills}", number_format($kills)));
            $player->sendMessage((string) new Message("stats-body-killstreak", "{killstreak}", number_format($killstreak)));
            $player->sendMessage((string) new Message("stats-body-deaths", "{deaths}", number_format($deaths)));
            $player->sendMessage((string) new Message("stats-body-kdr", "{kdr}", $kdr));
        }
    }
    
    public function getTopKills(int $limit = 10) : array{
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT player, kills FROM stats ORDER BY kills DESC LIMIT :limit");
        
        try {
            $stmt->bindValue(":limit", $limit, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            $kills = [];
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $kills[] = $row;
            }
            
            $result->finalize();
            
            return $kills;
        } finally {
            $stmt->close();
        }
    }
    
    public function getTopKillStreak(int $limit = 10) : array{
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT player, killstreak FROM stats ORDER BY killstreak DESC LIMIT :limit");
        
        try {
            $stmt->bindValue(":limit", $limit, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            $killstreak = [];
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $killstreak[] = $row;
            }
            
            $result->finalize();
            
            return $killstreak;
        } finally {
            $stmt->close();
        }
    }
    
    public function getTopDeaths(int $limit = 10) : array{
        $stmt = Database::getInstance()->getSQL()->prepare("SELECT player, deaths FROM stats ORDER BY deaths DESC LIMIT :limit");
        
        try {
            $stmt->bindValue(":limit", $limit, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            $deaths = [];
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $deaths[] = $row;
            }
            
            $result->finalize();
            
            return $deaths;
        } finally {
            $stmt->close();
        }
    }
}