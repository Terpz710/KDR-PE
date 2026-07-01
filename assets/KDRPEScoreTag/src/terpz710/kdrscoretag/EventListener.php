<?php

declare(strict_types=1);

namespace terpz710\kdrscoretag;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\Server;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\event\AddKillEvent;
use terpz710\kdrpe\event\AddKillstreakEvent;
use terpz710\kdrpe\event\ResetKillstreakEvent;
use terpz710\kdrpe\event\AddDeathEvent;

use Ifera\ScoreHud\event\TagsResolveEvent;

class EventListener implements Listener {

    public function onUserJoin(PlayerJoinEvent $event) : void{
        ScoreTag::updateTags($event->getPlayer());
    }

    public function onAddKill(AddKillEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            ScoreTag::updateTags($player);
        }
    }

    public function onAddKillstreak(AddKillstreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            ScoreTag::updateTags($player);
        }
    }

    public function onResetKillstreak(ResetKillstreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            ScoreTag::updateTags($player);
        }
    }

    public function onAddDeath(AddDeathEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            ScoreTag::updateTags($player);
        }
    }

    public function onTagsResolve(TagsResolveEvent $event) : void{
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $api = KDR::getInstance();
        $kills = $api->getKills($player);
        $killstreak = $api->getKillstreak($player);
        $deaths = $api->getDeaths($player);
        $kdr = $api->getKDR($player);

        switch ($tag->getName()) {
            case "kdrpe.kills":
                $tag->setValue(number_format($kills));
            break;

            case "kdrpe.killstreak":
                $tag->setValue(number_format($killstreak));
            break;

            case "kdrpe.deaths":
                $tag->setValue(number_format($deaths));
            break;

            case "kdrpe.kdr":
              $tag->setValue((string) $kdr);
            break;
        }
    }
}