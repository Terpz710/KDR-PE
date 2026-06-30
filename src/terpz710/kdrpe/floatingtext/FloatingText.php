<?php

declare(strict_types=1);

namespace terpz710\kdrpe\floatingtext;

use pocketmine\Server;

use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;

use pocketmine\utils\Config;

use terpz710\kdrpe\Core;

use terpz710\kdrpe\leaderboard\Leaderboard;

class FloatingText {
    
    public static array $floatingText = [];

    public static function create(Position $position, string $tag, string $text) : void{
        $world = $position->getWorld();

        if ($world !== null) {
            $chunk = $world->getOrLoadChunkAtPosition($position);
            if ($chunk !== null) {
                $floatingText = new FloatingTextParticle(str_replace("{line}", "\n", $text));

                if (array_key_exists($tag, self::$floatingText)) {
                    self::remove($tag);
                }

                self::$floatingText[$tag] = [$position, $floatingText];
                
                $world->addParticle($position, $floatingText, $world->getPlayers());
                self::saveToFile();
            } else {
                Server::getInstance()->getLogger()->warning("Chunk not loaded for floating text with tag '$tag'.");
            }
        }
    }

    public static function remove(string $tag) : void{
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        
        $floatingText = self::$floatingText[$tag][1];
        
        $floatingText->setInvisible();
        
        self::$floatingText[$tag][1] = $floatingText;
        
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        unset(self::$floatingText[$tag]);
        self::saveToFile();
    }

    public static function update(string $tag, string $text) : void{
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        
        $floatingText = self::$floatingText[$tag][1];
        
        $floatingText->setText(str_replace("{line}", "\n", $text));
        
        self::$floatingText[$tag][1] = $floatingText;
        
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        self::saveToFile();
    }

    public static function makeInvisible(string $tag) : void{
        if (array_key_exists($tag, self::$floatingText)) {
            $floatingText = self::$floatingText[$tag];
            
            $floatingText->setInvisible();
            
            self::$floatingText[$tag][1] = $floatingText;
            
            self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        }
    }

    public static function loadFromFile() : void{
        $filePath = Core::getInstance()->getDataFolder() . "database/floating_text.json";
        
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            foreach ($data as $tag => $textData) {
                $world = Server::getInstance()->getWorldManager()->getWorldByName($textData["world"]);
                if ($world !== null) {
                    $position = new Position($textData["x"], $textData["y"], $textData["z"], $world);
                    $chunk = $world->getOrLoadChunkAtPosition($position);
                    if ($chunk !== null) {
                        self::create($position, $tag, $textData["text"]);
                    } else {
                        Server::getInstance()->getLogger()->warning("Chunk not loaded for floating text with tag '$tag'.");
                    }
                }
            }
        }
    }

    public static function saveToFile() : void{
        $filePath = new Config(Core::getInstance()->getDataFolder() . "database/floating_text.json");

        $data = [];

        foreach (self::$floatingText as $tag => [$position, $floatingText]) {
            $data[$tag] = [
                "text" => str_replace("\n", "{line}", $floatingText->getText()),
                "x" => $position->x,
                "y" => $position->y,
                "z" => $position->z,
                "world" => $position->getWorld()->getFolderName(),
            ];
        }

        $filePath->setAll($data);
        $filePath->save();
    }

    public static function saveFile() : string{
        return json_encode(self::$floatingText, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    public static function canSpawn() : bool{
        $filePath = Core::getInstance()->getDataFolder() . "database/floating_text.json";
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        $data = json_decode(file_get_contents($filePath), true);
        
        if (!is_array($data)) {
            return false;
        }
        
        $tags = [
            Leaderboard::FTEXT_ID_KILL,
            Leaderboard::FTEXT_ID_KILLSTREAK,
            Leaderboard::FTEXT_ID_DEATH
        ];
        
        foreach ($tags as $tag) {
            if (isset($data[$tag])) {
                return true;
            }
        }
        
        return false;
    }
}