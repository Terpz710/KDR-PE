<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\leaderboard\DeathLeaderboard;

use CortexPE\Commando\BaseCommand;

class TopDeathCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.topdeath");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $death_keaderboard = DeathLeaderboard::getInstance()->getTopDeaths();
        $sender->sendMessage("§l§c===== Top Deaths Leaderboard =====");

        $rank = 1;
        foreach ($death_keaderboard as $name => $deaths) {
            $sender->sendMessage("§e{$rank}. {$name} - {$deaths} deaths");
            $rank++;
        }
        $sender->sendMessage("§l§c=================================");
    }
}
