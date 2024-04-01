<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\API;

use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;
use pocketmine\world\World;
use pocketmine\Server;

class FloatingKDRAPI {
    public static array $floatingText = [];

    public static function create(Position $position, string $tag, string $text, string $ftFolderPath): void {
        $floatingText = new FloatingTextParticle(str_replace("{line}", "\n", $text));
        if (array_key_exists($tag, self::$floatingText)) {
            self::remove($tag, $ftFolderPath);
        }
        self::$floatingText[$tag] = [$position, $floatingText];
        $position->getWorld()->addParticle($position, $floatingText, $position->getWorld()->getPlayers());
        self::saveToFile($ftFolderPath);
    }

    public static function remove(string $tag, string $ftFolderPath): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        $floatingText = self::$floatingText[$tag][1];
        $floatingText->setInvisible();
        self::$floatingText[$tag][1] = $floatingText;
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        unset(self::$floatingText[$tag]);
        self::saveToFile($ftFolderPath);
    }

    public static function update(string $tag, string $text, string $ftFolderPath): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }
        $floatingText = self::$floatingText[$tag][1];
        $floatingText->setText(str_replace("{line}", "\n", $text));
        self::$floatingText[$tag][1] = $floatingText;
        self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        self::saveToFile($ftFolderPath);
    }

    public static function makeInvisible(string $tag): void {
        if (array_key_exists($tag, self::$floatingText)) {
            $floatingText = self::$floatingText[$tag][1];
            $floatingText->setInvisible();
            self::$floatingText[$tag][1] = $floatingText;
            self::$floatingText[$tag][0]->getWorld()->addParticle(self::$floatingText[$tag][0], $floatingText, self::$floatingText[$tag][0]->getWorld()->getPlayers());
        }
    }

    public static function loadFromFile(string $filePath, string $ftFolderPath): void {
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            foreach ($data as $tag => $textData) {
                $position = new Position($textData["x"], $textData["y"], $textData["z"], Server::getInstance()->getWorldManager()->getWorldByName($textData["world"]));
                self::create($position, $tag, $textData["text"], $ftFolderPath);
            }
        }
    }

    public static function saveToFile(string $ftFolderPath): void {
        $filePath = $ftFolderPath . DIRECTORY_SEPARATOR . "floating_text_data.json";
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
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function saveFile(): string {
        return json_encode(self::$floatingText, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
