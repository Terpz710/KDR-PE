<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\Main;

class KDRCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct('kdr', 'Shows your KDR', '/kdr');
        $this->setPermission('kdr-pe.command.kdr');
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if ($sender instanceof Player) {
            $playerName = $sender->getName();
            $kills = $this->kdrManager->getKills($playerName);
            $deaths = $this->kdrManager->getDeaths($playerName);
            $kdr = ($deaths === 0) ? $kills : round($kills / $deaths, 2);
            $formattedName = ucwords(strtolower($playerName));
            $sender->sendMessage("-----------§e{$formattedName}'s Stats§f-----------");
            $sender->sendMessage("Kills: §e{$kills}");
            $sender->sendMessage("Deaths: §e{$deaths}");
            $sender->sendMessage("KDR: §e{$kdr}");
            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game!");
        }
        return false;
    }
}
