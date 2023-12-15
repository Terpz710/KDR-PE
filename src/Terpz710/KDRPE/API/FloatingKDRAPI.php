<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\API;

use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\Server;

class FloatingKDRAPI {
    private static array $floatingText = [];

    public static function create(Position $position, string $tag, string $text, string $ftFolderPath): void {
        $floatingText = new FloatingTextParticle(str_replace('{line}', "\n", $text));

        if (array_key_exists($tag, self::$floatingText)) {
            self::remove($tag, $ftFolderPath);
        }

        self::$floatingText[$tag] = ['position' => $position, 'text' => $text, 'particle' => $floatingText];
        $position->getWorld()->addParticle($position, $floatingText, $position->getWorld()->getPlayers());
    }

    public static function remove(string $tag, string $ftFolderPath): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }

        $position = self::$floatingText[$tag]['position'];
        $floatingText = self::$floatingText[$tag]['particle'];
        $floatingText->setInvisible();
        $position->getWorld()->addParticle($position, $floatingText, $position->getWorld()->getPlayers());
        unset(self::$floatingText[$tag]);
    }

    public static function update(string $tag, string $text, string $ftFolderPath): void {
        if (!array_key_exists($tag, self::$floatingText)) {
            return;
        }

        $position = self::$floatingText[$tag]['position'];
        $floatingText = self::$floatingText[$tag]['particle'];
        $floatingText->setText(str_replace('{line}', "\n", $text));
        self::$floatingText[$tag]['text'] = $text;
        $position->getWorld()->addParticle($position, $floatingText, $position->getWorld()->getPlayers());
    }

    public static function loadFromFile(string $filePath, string $ftFolderPath): void {
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            foreach ($data as $tag => $textData) {
                $position = new Position($textData['x'], $textData['y'], $textData['z'], Server::getInstance()->getWorldManager()->getWorldByName($textData['world']));
                self::create($position, $tag, $textData['text'], $ftFolderPath);
            }
        }
    }

    public static function getFloatingTextData(): array {
        return self::$floatingText;
    }

    public static function saveToFile(string $filePath): void {
        $data = [];

        foreach (self::$floatingText as $tag => $textData) {
            $position = $textData['position'];
            $data[$tag] = [
                'x' => $position->x,
                'y' => $position->y,
                'z' => $position->z,
                'world' => $position->getWorld()->getFolderName(),
                'text' => $textData['text'],
            ];
        }

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        }
    }