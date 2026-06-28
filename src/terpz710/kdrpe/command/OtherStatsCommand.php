<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

use pocketmine\player\Player;

use terpz710\kdrpe\Core;

use terpz710\kdrpe\api\KDR;

class OtherStatsCommand extends KDRCommand {
    
    public function __construct(protected Core $plugin) {
        parent::__construct("seekdr", $this->plugin);
        $this->setDescription("Checkout someone else's current KDR stats");
        $this->setUsage("/seekdr <name>");
        $this->setPermission("kdrpe.otherstats");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }
        
        if (!isset($args[0])) {
            throw new InvalidCommandSyntaxException();
            return;
        }
        
        KDR::getInstance()->fetchStats($sender, true, $args[0]);
    }
}