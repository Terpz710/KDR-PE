<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use Terpz710\KDRPE\Main;

class SeeKillStreakCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("seekillstreak", "View the kill streak of another player", "/seekillstreak <player>");
        $this->setPermission("kdrpe.command.seekillstreak");
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

        if (count($args) !== 1) {
            $sender->sendMessage("Usage: /seekillstreak <player>");
            return;
        }

        $targetName = $args[0];
        $target = $this->plugin->getServer()->getPlayerExact($targetName);

        if ($target === null) {
            $sender->sendMessage("Player not found.");
            return;
        }

        $killStreak = $this->plugin->getKillStreak($target->getName());
        $sender->sendMessage("$targetName's current kill streak: $killStreak");
    }
}
