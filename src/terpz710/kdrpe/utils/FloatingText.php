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
    public static array $cachedData = [];

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
                self::$cachedData[$tag] = [
                    "text" => $text,
                    "position" => $position
                ];

                $world->addParticle($position, $floatingText, $world->getPlayers());
                self::saveToFile(Main::getInstance()->getDataFolder());
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
        unset(self::$cachedData[$tag]);
        self::saveToFile(Main::getInstance()->getDataFolder());
    }

    public static function update(string $tag, string $text): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        $floatingText = self::$floatingText[$tag][1];
        $floatingText->setText(str_replace("{line}", "\n", $text));
        self::$floatingText[$tag][1] = $floatingText;
        self::$cachedData[$tag]['text'] = $text;
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        self::saveToFile(Main::getInstance()->getDataFolder());
    }

    public static function makeInvisible(string $tag): void {
        if (array_key_exists($tag, self::$floatingText)) {
            $floatingText = self::$floatingText[$tag][1];
            $floatingText->setInvisible();
            self::$floatingText[$tag][1] = $floatingText;
            self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        }
    }

    public static function loadFromFile(string $filePath): void {
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            foreach ($data as $tag => $textData) {
                $world = Server::getInstance()->getWorldManager()->getWorldByName($textData["world"]);
                if ($world !== null) {
                    $position = new Position($textData["x"], $textData["y"], $textData["z"], $world);
                    $chunk = $world->getOrLoadChunkAtPosition($position);
                    if ($chunk !== null) {
                        self::create($position, $tag, $textData["text"]);
                        // Cache the loaded data
                        self::$cachedData[$tag] = [
                            "text" => $textData["text"],
                            "position" => $position
                        ];
                    } else {
                        Server::getInstance()->getLogger()->warning("Chunk not loaded for floating text with tag '$tag'.");
                    }
                }
            }
        }
    }

    public static function saveToFile(string $dataPath): void {
        $filePath = new Config($dataPath . "floating_text_data.json", Config::JSON);

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

    public static function saveFile(): string {
        return json_encode(self::$floatingText, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function getCachedData(): array {
        return self::$cachedData;
    }
}
