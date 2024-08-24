<?php

declare(strict_types=1);

namespace terpz710\kdrpe\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\WorldUnloadEvent;

use pocketmine\player\Player;

use terpz710\kdrpe\utils\FloatingText;
use terpz710\kdrpe\utils\KdrScoreHud;

use terpz710\kdrpe\Main;

class KdrEvent implements Listener {

    private $kdrManager;
    private $scoreHud;

    public function __construct() {
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function onChunkLoad(ChunkLoadEvent $event) {
        FloatingText::loadFromFile(Main::getInstance()->getDataFolder() . "floating_text_data.json");
    }

    public function onChunkUnload(ChunkUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function onWorldUnload(WorldUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function onEntityTeleport(EntityTeleportEvent $event) {
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

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $this->kdrManager->initializePlayerData($player->getName());
        $cause = $player->getLastDamageCause();
        $this->scoreHud = new KdrScoreHud();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            if ($damager instanceof Player) {
                $this->kdrManager->incrementKill($damager->getName());
                $this->scoreHud->updateScoreHudTags($damager);

                if (isset($this->kdrManager->killStreaks[$damager->getName()])) {
                    $this->kdrManager->killStreaks[$damager->getName()]++;
                } else {
                    $this->kdrManager->killStreaks[$damager->getName()] = 1;
                }
                $this->kdrManager->handleKillStreak($damager->getName(), $this->kdrManager->killStreaks[$damager->getName()]);
            }
        }

        $this->kdrManager->incrementDeath($player->getName());
        $this->kdrManager->resetKillStreak($player->getName());
        $this->scoreHud->updateScoreHudTags($player);
        $this->kdrManager->updateFloatingText();
        $this->kdrManager->saveKillStreakData();
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->scoreHud = new KdrScoreHud();
        $this->kdrManager->initializePlayerData($player->getName());
        $this->scoreHud->updateScoreHudTags($player);
    }

    public function onPlayerQuit(PlayerQuitEvent $event) {
        FloatingText::saveFile();
    }
}
