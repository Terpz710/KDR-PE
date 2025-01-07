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

class KDRCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("kdr");
        $this->setDescription("View your KDR stats");
        $this->setPermission("kdrpe.kdr");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $kdrManager = $this->plugin->getKDRManager();

        $kills = $kdrManager->getKills($sender);
        $deaths = $kdrManager->getDeaths($sender);
        $kdr = $kdrManager->getKDR($sender);
        $killstreak = $kdrManager->getKillStreak($sender);

        $sender->sendMessage("§l=====§e KDR stats §f=====");
        $sender->sendMessage("kills:§e $kills");
        $sender->sendMessage("deaths:§e $deaths");
        $sender->sendMessage("killStreak:§e $killstreak");
        $sender->sendMessage("KDR:§e $kdr");
        $sender->sendMessage("§l====================");

        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}