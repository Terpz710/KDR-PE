<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands\leaderboards\floatingtext;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use pocketmine\world\Position;

use terpz710\kdrpe\Main;

use terpz710\kdrpe\leaderboards\KillStreakLeaderboard;

use terpz710\kdrpe\floatingtext\FloatingText;

class KillStreakLeaderboardCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("killstreakleaderboard");
        $this->setDescription("Spawn a floating text showing the top killerstreak");
        $this->setAliases(["killstreaklb", "kslb"]);
        $this->setPermission("kdrpe.killstreakleaderboard");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $position = $sender->getPosition();
        $killStreakLeaderboard = KillStreakLeaderboard::getInstance()->getTopKillStreak();
        $text = "§l§a-=Top KillStreak Leaderboard=-\n";

        $rank = 1;
        foreach ($killStreakLeaderboard as $username => $killstreak) {
            $text .= "§r§e{$rank}. {$username} - {$killstreak} killstreak\n";
            $rank++;
        }

        FloatingText::create($position, "killstreak_leaderboard", $text);
        $sender->sendMessage(TextFormat::GREEN . "KillStreak leaderboard floating text created at your location!");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}