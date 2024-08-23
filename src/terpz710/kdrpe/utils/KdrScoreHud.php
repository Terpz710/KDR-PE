<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\player\Player;

use terpz710\kdrpe\Main;

use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\event\TagsResolveEvent;

class KdrScoreHud {

    private $kdrManager;

    public function __construct() {
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function updateScoreHudTags(Player $player) {
        if (class_exists(ScoreHud::class)) {
            $kills = $this->kdrManager->getKills($player->getName());
            $deaths = $this->kdrManager->getDeaths($player->getName());

            if ($deaths === 0) {
                $kdr = $kills;
            } else {
                $kdr = $kills / $deaths;
            }
            $kdr = round($kdr, 3);
            $killStreak = $this->kdrManager->getKillStreak($player->getName());
            $ev = new PlayerTagsUpdateEvent(
                $player,
                [
                    new ScoreTag("kdrpe.kills", (string)$kills),
                    new ScoreTag("kdrpe.deaths", (string)$deaths),
                    new ScoreTag("kdrpe.kdr", (string)$kdr),
                    new ScoreTag("kdrpe.killstreak", (string)$killStreak),
                ]
            );
            $ev->call();
        }
    }

    public function onTagResolve(TagsResolveEvent $event) {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $kills = $this->kdrManager->getKills($player->getName());
        $deaths = $this->kdrManager->getDeaths($player->getName());

        if ($deaths === 0) {
            $kdr = $kills;
        } else {
            $kdr = $kills / $deaths;
        }
        $kdr = round($kdr, 3);
        $killStreak = $this->kdrManager->getKillStreak($player->getName());
        match ($tag->getName()) {
            "kdrpe.kills" => $tag->setValue((string)$kills),
            "kdrpe.deaths" => $tag->setValue((string)$deaths),
            "kdrpe.kdr" => $tag->setValue((string)($kdr)),
            "kdrpe.killstreak" => $tag->setValue((string)$killStreak),
            default => null,
        };
    }
}
