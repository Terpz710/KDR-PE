<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\Command;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\TargetPlayerArgument;

class SeeKDRCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.seekdr");

        $this->registerArgument(0, new TargetPlayerArgument(false, "player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $name = $args["player"];

        $kdr = KDRSavedData::getInstance();

        if(!$kdr->hasAccount($name)){
            $sender->sendMessage("The player §e" . $args["name"] . "§f does not exist!");
            return;
        }

        $kills = $kdr->getKills($name);
        $deaths = $kdr->getDeaths($name);
        $killstreak = $kdr->getKillStreak($name);
        $kd = $kdr->getKDR($name);

        $sender->sendMessage("§l=====§e " . $targetName . "'s KDR stats §f=====");
        $sender->sendMessage("kills:§e " . number_format($kills));
        $sender->sendMessage("deaths:§e " . number_format($deaths));
        $sender->sendMessage("killstreak:§e " . number_format($killstreak));
        $sender->sendMessage("KDR:§e " . $kd);
        $sender->sendMessage("§l===========================");
    }
}
