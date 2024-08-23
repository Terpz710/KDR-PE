<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\Main;

class SeeKillStreakCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct("seekillstreak", "View the kill streak of another player", "/seekillstreak <player>");
        $this->setPermission("kdrpe.command.seekillstreak");
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() :Plugin{
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

        if (count($args) !== 1) {
            $sender->sendMessage("Usage: /seekillstreak <player>");
            return false;
        }

        $targetName = $args[0];
        $dataPath = $this->plugin->getDataFolder() . 'KDR' . DIRECTORY_SEPARATOR . 'data.json';
        $playerData = json_decode(file_get_contents($dataPath), true);

        if (isset($playerData[$targetName])) {
            $killStreak = $this->kdrManager->getKillStreak($targetName);
            $sender->sendMessage("-----------§e{$targetName}'s KillStreak§f-----------");
            $sender->sendMessage("KillStreak: §e{$killStreak}");
            return true;
        } else {
            $sender->sendMessage("§l§c[!]§r§f KillStreak data not found for {$targetName}!");
            return false;
        }
    }
}
