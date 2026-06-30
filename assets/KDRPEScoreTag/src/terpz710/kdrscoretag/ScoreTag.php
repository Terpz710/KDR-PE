<?php

declare(strict_types=1);

namespace terpz710\kdrscoretag;

use pocketmine\player\Player;

use terpz710\kdrpe\api\KDR;

use Ifera\scorehud\scoreboard\ScoreTag as SHScoreTag;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;

class ScoreTag {

    public static function updateTags(Player $player) : void{
        $api = KDR::getInstance();
        $kills = $api->getKills($player);
        $killstreak = $api->getKillstreak($player);
        $deaths = $api->getDeaths($player);
        $kdr = $api->getKDR($player);

        $e = new PlayerTagsUpdateEvent(
            $player,
            [
                new SHScoreTag("kdrpe.kills", number_format($kills)),
                new SHScoreTag("kdrpe.killstreak", number_format($killstreak)),
                new SHScoreTag("kdrpe.deaths", number_format($deaths)),
                new SHScoreTag("kdrpe.kdr", (string) $kdr)
            ]
        );

        $e->call();
    }
}