<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use terpz710\kdrpe\Main;

class SeeKDRCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("seekdr");
        $this->setDescription("View another player's KDR stats");
        $this->setUsage("Usage: /seekdr <player>");
        $this->setPermission("kdrpe.seekdr");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }

        $targetPlayer = $sender->getServer()->getPlayerByPrefix($args[0]);

        $kdrManager = Main::getInstance()->getKDRManager();

        if (!$kdrManager->hasKDRProfile($targetPlayer)) {
            $sender->sendMessage(TextFormat::RED . $args[0] . " does not exist!");
            return false;
        }

        $kills = $kdrManager->getKills($targetPlayer);
        $deaths = $kdrManager->getDeaths($targetPlayer);
        $kdr = $kdrManager->getKDR($targetPlayer);
        $killstreak = $kdrManager->getKillStreak($targetPlayer);
        $name = $targetPlayer->getName();

        $sender->sendMessage("§l=====§e {$name}'s KDR stats §f=====");
        $sender->sendMessage("kills:§e $kills");
        $sender->sendMessage("deaths:§e $deaths");
        $sender->sendMessage("killstreal:§e $killstreak");
        $sender->sendMessage("KDR:§e $kdr");
        $sender->sendMessage("§l===========================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}