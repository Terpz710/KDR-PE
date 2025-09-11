<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard;

use pocketmine\command\Command;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\leaderboard\KillLeaderboard;

use CortexPE\Commando\BaseCommand;

class TopKillCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.topkill");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $kill_keaderboard = KillLeaderboard::getInstance()->getTopKillers();
        $sender->sendMessage("§l§a===== Top Kills Leaderboard =====");

        $rank = 1;
        foreach ($kill_keaderboard as $name => $kills) {
            $sender->sendMessage("§e{$rank}. {$name} - {$kills} kills");
            $rank++;
        }
        $sender->sendMessage("§l§a===============================");     
    }
}