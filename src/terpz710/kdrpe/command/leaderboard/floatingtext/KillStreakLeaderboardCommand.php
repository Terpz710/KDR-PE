<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard\floatingtext;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\leaderboard\KillStreakLeaderboard;

use CortexPE\Commando\BaseCommand;

class KillStreakLeaderboardCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.killstreakleaderboard");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $position = $sender->getPosition();
        $killstreak_eaderboard = KillStreakLeaderboard::getInstance()->getTopKillStreak();
        $text = "§l§b-=Top KillStreak Leaderboard=-\n";

        $rank = 1;
        foreach ($killstreak_leaderboard as $username => $killstreak) {
            $text .= "§r§e{$rank}. {$username} - {$killstreak} killstreak\n";
            $rank++;
        }

        FloatingText::create($position, "killstreak_leaderboard", $text);
        $sender->sendMessage("KillStreak leaderboard floating text created at your location!");
    }
}
