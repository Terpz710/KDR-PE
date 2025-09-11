<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\WorldUnloadEvent;

use pocketmine\Server;

use terpz710\kdrpe\data\KDR;
use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\event\AddKillEvent;
use terpz710\kdrpe\event\AddDeathEvent;
use terpz710\kdrpe\event\AddKillStreakEvent;
use terpz710\kdrpe\event\ResetKillStreakEvent;

use terpz710\kdrpe\scoreboard\KDRScoreboard;

use terpz710\kdrpe\floatingtext\FloatingText;

use Ifera\ScoreHud\event\TagsResolveEvent;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $kdr = KDR::getInstance();

        if(!$kdr->hasAccount($player)){
            $kdr->createAccount($player);
        }
    }

    public function death(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        $kdr = KDRSavedData::getInstance();
        $cause = $player->getLastDamageCause();

        if($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();

            if($damager instanceof Player){
                $kdr->addKill($damager);
                $kdr->addKillStreak($damager);
            }
        }

        $kdr->addDeath($player);
        $kdr->resetKillStreak($player);
    }

    public function chunkLoad(ChunkLoadEvent $event) {
        FloatingText::loadFromFile();
    }

    public function chunkUnload(ChunkUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function worldUnload(WorldUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function teleport(EntityTeleportEvent $event) {
        $entity = $event->getEntity();
        
        if ($entity instanceof Player) {
            $fromWorld = $event->getFrom()->getWorld();
            $toWorld = $event->getTo()->getWorld();
        
            if ($fromWorld !== $toWorld) {
                foreach (FloatingText::$floatingText as $tag => [$position, $floatingText]) {
                    if ($position->getWorld() === $fromWorld) {
                        FloatingText::makeInvisible($tag);
                    }
                }
            }
        }
    }

    public function tagResolve(TagsResolveEvent $event) {
        $player = $event->getPlayer();
        $tag = $event->getTag();

        $kdr = KDRSavedData::getInstance();
        $kills = $kdr->getKills($player);
        $deaths = $kdr->getDeaths($player);
        $kd = $kdr->getKDR($player);
        $killstreak = $kdr->getKillStreak($player);

        match ($tag->getName()) {
            "kdrpe.kills" => $tag->setValue((string)number_format($kills)),
            "kdrpe.deaths" => $tag->setValue((string)number_format($deaths)),
            "kdrpe.kdr" => $tag->setValue((string)$kd),
            "kdrpe.killstreak" => $tag->setValue((string)number_format($killstreak)),
            default => null,
        };
    }

    public function addKill(AddKillEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }
    }

    public function addDeath(AddDeathEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }
    }

    public function killStreak(AddKillStreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }
    }

    public function resetKillStreak(ResetKillStreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }
    }
}
