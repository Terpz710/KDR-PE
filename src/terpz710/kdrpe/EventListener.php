<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\database\Database;

use terpz710\kdrpe\floatingtext\FloatingText;

class EventListener implements Listener {
    
    public function onUserLogin(PlayerLoginEvent $event) : void{
        $player = $event->getPlayer();
        $database = Database::getInstance();
        
        if ($database->isNew($player)) {
            $database->insertIntoDatabase($player);
        }

        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                (new Leaderboard(LeaderboardType::KILL))->updateFloatingText();
            }
        }

        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                (new Leaderboard(LeaderboardType::KILLSTREAK))->updateFloatingText();
            }
        }

        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                (new Leaderboard(LeaderboardType::DEATH))->updateFloatingText();
            }
        }
    }

    public function onUserDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        $kdr = KDR::getInstance();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            $kdr->addKill($damager);
            $kdr->addKillstreak($damager);
        }

        $kdr->addDeath($player);
        $kdr->resetKillstreak($player);
    }

    public function onChunkLoad(ChunkLoadEvent $event) : void{
        $config = Core::getInstance()->getConfig();
        
        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                FloatingText::loadFromFile();
            }
        }
    }

    public function onChunkUnload(ChunkUnloadEvent $event) : void{
        $config = Core::getInstance()->getConfig();
        
        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                FloatingText::saveFile();
            }
        }
    }

    public function onWorldUnload(WorldUnloadEvent $event) : void{
        $config = Core::getInstance()->getConfig();
        
        if ($config->get("enable-floatingtext")) {
            if (FloatingText::canSpawn()) {
                FloatingText::saveFile();
            }
        }
    }

    public function onEntityTeleport(EntityTeleportEvent $event) : void{
        $entity = $event->getEntity();
        
        if ($entity instanceof Player) {
            $fromWorld = $event->getFrom()->getWorld();
            $toWorld = $event->getTo()->getWorld();
            
            if ($config->get("enable-floatingtext")) {
                if ($fromWorld !== $toWorld) {
                    foreach (FloatingText::$floatingText as $tag => [$position, $floatingText]) {
                        if ($position->getWorld() === $fromWorld) {
                            FloatingText::makeInvisible($tag);
                        }
                    }
                }
            }
        }
    }
}