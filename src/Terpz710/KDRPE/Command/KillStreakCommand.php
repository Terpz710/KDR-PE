<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use Terpz710\KDRPE\Main;

class KillStreakCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("killstreak", "View your current kill streak", "/killstreak", ["ks"]);
        $this->setPermission("kdrpe.command.killstreak");
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $playerName = $sender->getName();
        $killStreak = $this->plugin->getKillStreak($playerName);
        $sender->sendMessage("-----------§e{$playerName}'s Stats§f-----------");
        $sender->sendMessage("KillStreak: §e{$killStreak}");
    }
}
