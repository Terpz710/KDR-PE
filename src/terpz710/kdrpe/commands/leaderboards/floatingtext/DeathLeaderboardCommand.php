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

use terpz710\kdrpe\leaderboards\DeathLeaderboard;

use terpz710\kdrpe\floatingtext\FloatingText;

class DeathLeaderboardCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("deathleaderboard");
        $this->setDescription("Spawn a floating text showing the top deaths");
        $this->setAliases(["deathlb", "dlb"]);
        $this->setPermission("kdrpe.deathleaderboard");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        $position = $sender->getPosition();
        $deathLeaderboard = DeathLeaderboard::getInstance()->getTopDeaths();
        $text = "§l§c-=Top Deaths Leaderboard=-\n";

        $rank = 1;
        foreach ($deathLeaderboard as $username => $deaths) {
            $text .= "§r§e{$rank}. {$username} - {$deaths} deaths\n";
            $rank++;
        }

        FloatingText::create($position, "death_leaderboard", $text);
        $sender->sendMessage(TextFormat::GREEN . "Death leaderboard floating text created at your location!");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}