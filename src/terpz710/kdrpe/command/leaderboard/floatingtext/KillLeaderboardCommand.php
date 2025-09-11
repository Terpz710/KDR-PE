<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard\floatingtext;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\leaderboard\KillLeaderboard;

use CortexPE\Commando\BaseCommand;

class KillLeaderboardCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.killleaderboard");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $position = $sender->getPosition();
        $kill_leaderboard = KillLeaderboard::getInstance()->getTopKillers();
        $text = "§l§a-=Top Kills Leaderboard=-\n";

        $rank = 1;
        foreach ($kill_leaderboard as $name => $kills) {
            $text .= "§r§e{$rank}. {$name} - {$kills} kills\n";
            $rank++;
        }

        FloatingText::create($position, "kill_leaderboard", $text);
        $sender->sendMessage("Kill leaderboard floating text created at your location!");
    }
}
