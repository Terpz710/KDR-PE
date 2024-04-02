<?php

declare(strict_types=1);

namespace Terpz710\KDRPE;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;
use pocketmine\utils\Config;

use Terpz710\KDRPE\Command\KDRCommand;
use Terpz710\KDRPE\Command\SeeKDRCommand;
use Terpz710\KDRPE\Command\TopKillCommand;
use Terpz710\KDRPE\Command\TopKillFTCommand;
use Terpz710\KDRPE\API\FloatingKDRAPI;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\event\TagsResolveEvent;

class Main extends PluginBase implements Listener {

    private static $instance;

    public function onLoad(): void {
        self::$instance = $this;
    }

    public function onEnable(): void {
        $this->getServer()->getCommandMap()->registerAll("KDR-PE", [
            new KDRCommand($this),
            new SeeKDRCommand($this),
            new TopKillCommand($this),
            new TopKillFTCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $kdrFolderPath = $this->getDataFolder() . 'KDR';
        if (!is_dir($kdrFolderPath)) {
        @mkdir($kdrFolderPath);
        }

        $dataPath = $kdrFolderPath . DIRECTORY_SEPARATOR . 'data.json';
        if (!file_exists($dataPath)) {
            $initialData = [];
            file_put_contents($dataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }

        $ftDataPath = $this->getDataFolder() . DIRECTORY_SEPARATOR . 'floating_text_data.json';
        if (!file_exists($ftDataPath)) {
            $initialData = [];
            file_put_contents($ftDataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }

    public function onDisable(): void {
        FloatingKDRAPI::saveFile();
    }

    public static function getInstance(): ?Main {
        return self::$instance;
    }

    public function onChunkLoad(ChunkLoadEvent $event): void {
        $filePath = $this->getDataFolder() . "floating_text_data.json";
        FloatingKDRAPI::loadFromFile($filePath);
    }

    public function onChunkUnload(ChunkUnloadEvent $event): void {
        FloatingKDRAPI::saveFile();
    }

    public function onWorldUnload(WorldUnloadEvent $event): void {
        FloatingKDRAPI::saveFile();
    }

    public function onEntityTeleport(EntityTeleportEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $fromWorld = $event->getFrom()->getWorld();
            $toWorld = $event->getTo()->getWorld();
        
            if ($fromWorld !== $toWorld) {
                foreach (FloatingKDRAPI::$floatingText as $tag => [$position, $floatingText]) {
                    if ($position->getWorld() === $fromWorld) {
                        FloatingKDRAPI::makeInvisible($tag);
                    }
                }
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $this->initializePlayerData($player->getName());

        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            if ($damager instanceof Player) {
                $this->incrementKill($damager->getName());
                $this->updateScoreHudTags($damager);
            }
        }

        $this->incrementDeath($player->getName());
        $this->updateScoreHudTags($player);
        $this->updateFloatingText();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $this->initializePlayerData($playerName);
        $this->updateScoreHudTags($player);
    }

    private function initializePlayerData(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';

        $playerData = json_decode(file_get_contents($dataPath), true);

        if (!isset($playerData[$playerName])) {
            $playerData[$playerName] = ['kills' => 0, 'deaths' => 0];
            file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
            $this->updateScoreHudTags($this->getServer()->getPlayerExact($playerName));
        }
    }

    private function incrementKill(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['kills']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
    }

    private function incrementDeath(string $playerName): void {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        $playerData[$playerName]['deaths']++;
        file_put_contents($dataPath, json_encode($playerData, JSON_PRETTY_PRINT));
    }

    private function updateFloatingText() {
        $filePath = $this->getDataFolder() . "floating_text_data.json";
        $text = $this->getFloatingText();
        FloatingKDRAPI::update($text, $filePath);
    }

    private function getFloatingText(): string {
        $topKillData = $this->getTopKills();

        $text = "-----------§eTOP KILLS§f-----------\n";

        $rank = 1;
        foreach ($topKillData as $playerName => $kills) {
            $text .= "§e{$rank}. §f{$playerName}: §e{$kills}\n";
            $rank++;

            if ($rank > 10) {
                break;
            }
        }

        return $text;
    }


    public function getPlayerData(): array {
        $dataPath = $this->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        return $playerData ?? [];
    }

    public function getKills(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['kills'] ?? 0;
    }

    public function getDeaths(string $playerName): int {
        $playerData = $this->getPlayerData();

        return $playerData[$playerName]['deaths'] ?? 0;
    }

    public function getTopKills(): array {
        $playerData = $this->getPlayerData();
        $topKills = [];

        foreach ($playerData as $playerName => $data) {
            $kills = $data['kills'] ?? 0;
            $topKills[$playerName] = $kills;
        }

        arsort($topKills);

        return array_slice($topKills, 0, 10);
    }

    public function updateScoreHudTags(Player $player): void
    {
        if (class_exists(ScoreHud::class)) {
            $kills = $this->getKills($player->getName());
            $deaths = $this->getDeaths($player->getName());

            if ($deaths === 0) {
                $kdr = $kills;
            } else {
                $kdr = $kills / $deaths;
            }
            $kdr = round($kdr, 3);
            $ev = new PlayerTagsUpdateEvent(
                $player,
                [
                    new ScoreTag("kdrpe.kills", (string)$kills),
                    new ScoreTag("kdrpe.deaths", (string)$deaths),
                    new ScoreTag("kdrpe.kdr", (string)$kdr)
                ]
            );
            $ev->call();
        }
    }

    public function onTagResolve(TagsResolveEvent $event): void {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $kills = $this->getKills($player->getName());
        $deaths = $this->getDeaths($player->getName());

        if ($deaths === 0) {
            $kdr = $kills;
        } else {
            $kdr = $kills / $deaths;
        }
        $kdr = round($kdr, 3);

        match ($tag->getName()) {
            "kdrpe.kills" => $tag->setValue((string)$kills),
            "kdrpe.deaths" => $tag->setValue((string)$deaths),
            "kdrpe.kdr" => $tag->setValue((string)($kdr)),
            default => null,
        };
    }
}
