<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard;

use pocketmine\command\Command;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\leaderboard\KillStreakLeaderboard;

use CortexPE\Commando\BaseCommand;

class TopKillStreakCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.topkillstreak");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $killstreak_leaderboard = KillStreakLeaderboard::getInstance()->getTopKillStreak();
        $sender->sendMessage("§l§b===== Top KillStreak Leaderboard =====");

        $rank = 1;
        foreach ($killstreak_leaderboard as $name => $killstreak) {
            $sender->sendMessage("§e{$rank}. {$name} - {$killstreak} killstreak");
            $rank++;
        }
        $sender->sendMessage("§l§b===================================");
    }
}