<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands\leaderboards;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\leaderboards\KillStreakLeaderboard;

class TopKillStreakCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("topkillstreak");
        $this->setDescription("View the top players with the most killstreak");
        $this->setAliases(["topkillstreaks", "tks"]);
        $this->setPermission("kdrpe.topkillstreak");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $killstreakLeaderboard = KillStreakLeaderboard::getInstance()->getTopKillStreak();
        $sender->sendMessage("§l§a===== Top KillStreak Leaderboard =====");

        $rank = 1;
        foreach ($killstreakLeaderboard as $username => $killstreak) {
            $sender->sendMessage("§e{$rank}. {$username} - {$killstreak} killstreak");
            $rank++;
        }
        $sender->sendMessage("§l§a===================================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
