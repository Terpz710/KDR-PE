<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\Command;

use pocketmine\player\Player;

use terpz710\kdrpe\data\saved\KDRSavedData;

use CortexPE\Commando\BaseCommand;

class KDRCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("kdrpe.kdr");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used ingame!");
            return;
        }

        $kdr = KDRSavedData::getInstance();

        $kills = $kdr->getKills($sender);
        $deaths = $kdr->getDeaths($sender);
        $killstreak = $kdr->getKillStreak($sender);
        $kd = $kdr->getKDR($sender);

        $sender->sendMessage("§l=====§e KDR stats §f=====");
        $sender->sendMessage("kills:§e " . $kills);
        $sender->sendMessage("deaths:§e " . $deaths);
        $sender->sendMessage("killStreak:§e " . $killstreak);
        $sender->sendMessage("KDR:§e " . $kd);
        $sender->sendMessage("§l====================");
    }
}