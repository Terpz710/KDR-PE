<?php

declare(strict_types=1);

namespace terpz710\kdrpe\scorehud;

use pocketmine\player\Player;

use terpz710\kdrpe\Main;

use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;

class KDRScoreHud {

    public $kdrManager;

    public function __construct() {
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function updateTag(Player $player) {
        if (class_exists(ScoreHud::class)) {
            $kills = $this->kdrManager->getKills($player);
            $deaths = $this->kdrManager->getDeaths($player);
            $kdr = $this->kdrManager->getKDR($player);
            $killstreak = $this->kdrManager->getKillStreak($player);

            $ev = new PlayerTagsUpdateEvent(
                $player,
                [
                    new ScoreTag("kdrpe.kills", (string)$kills),
                    new ScoreTag("kdrpe.deaths", (string)$deaths),
                    new ScoreTag("kdrpe.kdr", (string)$kdr),
                    new ScoreTag("kdrpe.killstreak", (string)$killstreak),
                ]
            );
            $ev->call();
        }
    }

    public function onTagResolve(TagsResolveEvent $event) {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $kills = $this->kdrManager->getKills($player->getName());
        $deaths = $this->kdrManager->getDeaths($player);
        $kdr = $this->kdrManager->getKDR($player);
        $killstreak = $this->kdrManager->getKillStreak($player);

        match ($tag->getName()) {
            "kdrpe.kills" => $tag->setValue((string)$kills),
            "kdrpe.deaths" => $tag->setValue((string)$deaths),
            "kdrpe.kdr" => $tag->setValue((string)$kdr),
            "kdrpe.killstreak" => $tag->setValue((string)$killstreak),
            default => null,
        };
    }
}