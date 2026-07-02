<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\utils\Message;

use CortexPE\Commando\BaseCommand;

class StatsCommand extends BaseCommand {
    
    protected function prepare() : void{
        $this->setPermission("kdrpe.stats");
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }
        
        KDR::getInstance()->fetchStats($sender, false);
    }
}