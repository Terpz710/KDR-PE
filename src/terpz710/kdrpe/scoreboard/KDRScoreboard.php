<?php

declare(strict_types=1);

namespace terpz710\kdrpe\scoreboard;

use pocketmine\player\Player;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\data\saved\KDRSavedData;

use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;

final class KDRScoreboard {

    public static function updateTag(Player $player) {
        if (class_exists(ScoreHud::class)) {
            $data = KDRSavedData::getInstance();

            $kills = $data->getKills($player);
            $deaths = $data->getDeaths($player);
            $kdr = $data->getKDR($player);
            $killstreak = $data->getKillStreak($player);

            $ev = new PlayerTagsUpdateEvent(
                $player,
                [
                    new ScoreTag("kdrpe.kills", (string)number_format($kills)),
                    new ScoreTag("kdrpe.deaths", (string)number_format($deaths)),
                    new ScoreTag("kdrpe.kdr", (string)$kdr),
                    new ScoreTag("kdrpe.killstreak", (string)number_format($killstreak)),
                ]
            );
            $ev->call();
        }
    }
}