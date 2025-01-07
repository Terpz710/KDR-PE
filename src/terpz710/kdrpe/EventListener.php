<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\WorldUnloadEvent;

use terpz710\kdrpe\floatingtext\FloatingText;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $manager = Main::getInstance()->getKDRManager();
        $scorehud = Main::getInstance()->getKDRScoreHud();

        if (!$manager->hasKDRProfile($player)) {
            $manager->createKDRProfile($player);
        }

        $manager->updateUsername($player);
        $scorehud->updateTag($player);
    }

    public function death(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        $manager = Main::getInstance()->getKDRManager();
        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            if ($damager instanceof Player) {
                $manager->addKill($damager);
                $manager->addKillStreak($damager);
            }
        }

        $manager->addDeath($player);
        $manager->resetKillStreak($player);
    }

    public function chunkLoad(ChunkLoadEvent $event) {
        FloatingText::loadFromFile(Main::getInstance()->getDataFolder() . "floating_text.json");
    }

    public function chunkUnload(ChunkUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function worldUnload(WorldUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function teleport(EntityTeleportEvent $event) {
        $entity = $event->getEntity();
        $scorehud = Main::getInstance()->getKDRScoreHud();

        $scorehud->updateTag($entity);
        
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
}