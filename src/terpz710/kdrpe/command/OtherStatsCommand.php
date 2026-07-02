<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\Core;

use terpz710\kdrpe\database\Database;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\utils\Message;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\TargetPlayerArgument;

class OtherStatsCommand extends BaseCommand {
    
    protected function prepare() : void{
        $this->registerArgument(0, new TargetPlayerArgument("player"));
        $this->setPermission("kdrpe.otherstats");
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }

        if (Database::getInstance()->isNew($args["player"])) {
            $sender->sendMessage((string) new Message("player-not-found", "{player}", $args["player"]));
            return;
        }
        
        KDR::getInstance()->fetchStats($sender, true, $args["player"]);
    }
}