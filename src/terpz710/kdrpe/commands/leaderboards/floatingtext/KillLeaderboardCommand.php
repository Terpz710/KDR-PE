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

use terpz710\kdrpe\leaderboards\KillLeaderboard;

use terpz710\kdrpe\floatingtext\FloatingText;

class KillLeaderboardCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("killleaderboard");
        $this->setDescription("Spawn a floating text showing the top killers");
        $this->setAliases(["killlb", "klb"]);
        $this->setPermission("kdrpe.killleaderboard");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $this->plugin->getKDRManager()->data->reload();

        $position = $sender->getPosition();
        $killLeaderboard = KillLeaderboard::getInstance()->getTopKillers();
        $text = "§l§a-=Top Kills Leaderboard=-\n";

        $rank = 1;
        foreach ($killLeaderboard as $username => $kills) {
            $text .= "§r§e{$rank}. {$username} - {$kills} kills\n";
            $rank++;
        }

        FloatingText::create($position, "kill_leaderboard", $text);
        $sender->sendMessage(TextFormat::GREEN . "Kill leaderboard floating text created at your location!");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
