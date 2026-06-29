<?php

declare(strict_types=1);

namespace terpz710\kdrpe\database;

use SQLite3;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use terpz710\kdrpe\Core;

class Database {
    use SingletonTrait;
    
    protected SQLite3 $sql;
    
    private function __construct() {
        $folder = Core::getInstance()->getDataFolder() . "database/";
        
        @mkdir($folder);
        
        $this->sql = new SQLite3($folder . "database.db");
        
        $this->sql->exec("CREATE TABLE IF NOT EXISTS stats (player TEXT PRIMARY KEY, kills INT, killstreak INT, deaths INT);");
    }
    
    public function close() : void{
        $this->sql->close();
    }
    
    public function getSQL() : SQLite3{
        return $this->sql;
    }
    
    public function isNew(Player|string $player) : bool{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = $this->sql->prepare("SELECT * FROM stats WHERE player = :player;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            $data = $result->fetchArray(SQLITE3_ASSOC);
            
            $result->finalize();
            
            return $data === false ? true : false;
        } finally {
            $stmt->close();
        }
    }
    
    public function insertIntoDatabase(Player|string $player) : void{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = $this->sql->prepare("
            INSERT INTO
            stats
            (player, kills, killstreak, deaths)
            VALUES
            (:player, :kills, :killstreak, :deaths)
        ;");
        
        try {
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $stmt->bindValue(":kills", 0, SQLITE3_INTEGER);
            $stmt->bindValue(":killstreak", 0, SQLITE3_INTEGER);
            $stmt->bindValue(":deaths", 0, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            $result->finalize();
        } finally {
            $stmt->close();
        }
    }
}