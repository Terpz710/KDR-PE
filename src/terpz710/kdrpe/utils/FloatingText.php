<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use terpz710\kdrpe\Main;

class FloatingText {
    public static array $floatingText = [];

    public static function create(Position $position, string $tag, string $text): void {
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
            } else {
                Server::getInstance()->getLogger()->warning("Chunk not loaded for floating text with tag '$tag'.");
            }
        }
    }

    public static function remove(string $tag): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        $floatingText = self::$floatingText[$tag][1];
        $floatingText->setInvisible();
        self::$floatingText[$tag][1] = $floatingText;
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        unset(self::$floatingText[$tag]);
    }

    public static function update(string $tag, string $text): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        $floatingText = self::$floatingText[$tag][1];
        $floatingText->setText(str_replace("{line}", "\n", $text));
        self::$floatingText[$tag][1] = $floatingText;
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
    }

    public static function makeInvisible(string $tag): void {
        if (array_key_exists($tag, self::$floatingText)) {
            $floatingText = self::$floatingText[$tag][1];
            $floatingText->setInvisible();
            self::$floatingText[$tag][1] = $floatingText;
            self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        }
    }

    public static function loadFromFile(string $dataPath): void {
        $filePath = $dataPath . "floating_text_data.json";
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            foreach ($data as $tag => $textData) {
                $world = Server::getInstance()->getWorldManager()->getWorldByName($textData["world"]);
                if ($world !== null) {
                    $position = new Position($textData["x"], $textData["y"], $textData["z"], $world);
                    self::create($position, $tag, $textData["text"]);
                }
            }
        }
    }

    public static function saveToFile(string $dataPath): void {
        $data = [];
        foreach (self::$floatingText as $tag => [$position, $floatingText]) {
            $data[$tag] = [
                "x" => $position->getX(),
                "y" => $position->getY(),
                "z" => $position->getZ(),
                "world" => $position->getWorld()->getFolderName(),
                "text" => $floatingText->getText()
            ];
        }

        file_put_contents($dataPath . "floating_text_data.json", json_encode($data, JSON_PRETTY_PRINT));
    }
}
