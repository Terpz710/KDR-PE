<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\Main;

class KillStreakCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct("killstreak", "View your current kill streak", "/killstreak", ["ks"]);
        $this->setPermission("kdrpe.command.killstreak");
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $playerName = $sender->getName();
        $killStreak = $this->kdrManager->getKillStreak($playerName);
        $sender->sendMessage("-----------§e{$playerName}'s KillStreak§f-----------");
        $sender->sendMessage("KillStreak: §e{$killStreak}");
        return true;
    }
}
