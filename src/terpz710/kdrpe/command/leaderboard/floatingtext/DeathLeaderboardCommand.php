<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command\leaderboard\floatingtext;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use terpz710\kdrpe\floatingtext\FloatingText;
use terpz710\kdrpe\floatingtext\leaderboard\DeathLeaderboard;

use CortexPE\Commando\BaseCommand;

class DeathLeaderboardCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.deathleaderboard");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $position = $sender->getPosition();
        $death_leaderboard = DeathLeaderboard::getInstance()->getTopDeaths();
        $text = "§l§c-=Top Deaths Leaderboard=-\n";

        $rank = 1;
        foreach ($death_leaderboard as $name => $deaths) {
            $text .= "§r§e{$rank}. {$name} - {$deaths} deaths\n";
            $rank++;
        }

        FloatingText::create($position, "death_leaderboard", $text);
        $sender->sendMessage("Death leaderboard floating text created at your location!");
    }
}
