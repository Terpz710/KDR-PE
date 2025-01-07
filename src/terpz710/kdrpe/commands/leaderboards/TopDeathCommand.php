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

use terpz710\kdrpe\leaderboards\DeathLeaderboard;

class TopDeathCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("topdeath");
        $this->setDescription("View the top players with the most deaths");
        $this->setAliases(["topdeaths", "td"]);
        $this->setPermission("kdrpe.topdeath");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $deathLeaderboard = DeathLeaderboard::getInstance()->getTopDeaths();
        $sender->sendMessage("§l§c===== Top Deaths Leaderboard =====");

        $rank = 1;
        foreach ($deathLeaderboard as $username => $deaths) {
            $sender->sendMessage("§e{$rank}. {$username} - {$deaths} deaths");
            $rank++;
        }
        $sender->sendMessage("§l§c=================================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}