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

use terpz710\kdrpe\leaderboards\KillLeaderboard;

class TopKillCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("topkill");
        $this->setDescription("View the top players with the most kills");
        $this->setAliases(["topkills", "tk"]);
        $this->setPermission("kdrpe.topkill");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $killLeaderboard = KillLeaderboard::getInstance()->getTopKillers();
        $sender->sendMessage("§l§a===== Top Kills Leaderboard =====");

        $rank = 1;
        foreach ($killLeaderboard as $username => $kills) {
            $sender->sendMessage("§e{$rank}. {$username} - {$kills} kills");
            $rank++;
        }
        $sender->sendMessage("§l§a===============================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}