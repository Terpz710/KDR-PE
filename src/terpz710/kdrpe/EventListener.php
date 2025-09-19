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

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\kdrpe\data\KDR;
use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\event\AddKillEvent;
use terpz710\kdrpe\event\AddDeathEvent;
use terpz710\kdrpe\event\AddKillStreakEvent;
use terpz710\kdrpe\event\ResetKillStreakEvent;

use terpz710\kdrpe\scoreboard\KDRScoreboard;

use terpz710\kdrpe\floatingtext\FloatingText;
use terpz710\kdrpe\floatingtext\leaderboard\KillLeaderboard;
use terpz710\kdrpe\floatingtext\leaderboard\DeathLeaderboard;
use terpz710\kdrpe\floatingtext\leaderboard\KillStreakLeaderboard;

use Ifera\ScoreHud\event\TagsResolveEvent;

class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $kdr = KDR::getInstance();

        if(!$kdr->hasAccount($player)){
            $kdr->createAccount($player);
        }
    }

    public function onDeath(PlayerDeathEvent $event) : void {
        $victim = $event->getPlayer();
        $cause = $victim->getLastDamageCause();

        if($cause !== null && $cause->getEntity() !== null && $cause->getEntity() instanceof Player){
            $killer = $cause->getEntity();
            
            $ev = new PlayerKillEvent($killer, $victim);
            $ev->call();
        }
    }

    public function onChunkLoad(ChunkLoadEvent $event) {
        FloatingText::loadFromFile();
    }

    public function onChunkUnload(ChunkUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function onWorldUnload(WorldUnloadEvent $event) {
        FloatingText::saveFile();
    }

    public function onTeleport(EntityTeleportEvent $event) {
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

    public function onTagResolve(TagsResolveEvent $event) {
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

    public function onAddKill(AddKillEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        KDR::getInstance()->addKill($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }

        KillLeaderboard::getInstance()->updateKillFT();

        $player->sendMessage("Ive been fired up - Add Kill Event");
    }

    public function onAddDeath(AddDeathEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        KDR::getInstance()->addDeath($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }

        DeathLeaderboard::getInstance()->updateDeathFT();
    }

    public function onKillStreak(AddKillStreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        KDR::getInstance()->addKillStreak($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }

        KillStreakLeaderboard::getInstance()->updateKillStreakFT();

        $player->sendMessage("Ive been fired up - Add Kill Streak Event");
    }

    public function onResetKillStreak(ResetKillStreakEvent $event) : void{
        $name = $event->getName();
        $player = Server::getInstance()->getPlayerExact($name);

        KDR::getInstance()->resetKillStreak($name);

        if ($player !== null) {
            KDRScoreboard::updateTag($player);
        }

        KillStreakLeaderboard::getInstance()->updateKillStreakFT();
    }
}
