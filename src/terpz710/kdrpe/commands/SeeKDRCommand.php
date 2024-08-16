<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\Main;

class SeeKDRCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct('seekdr', 'See other players KDR', '/seekdr <player>');
        $this->setPermission("kdr-pe.command.seekdr");
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (count($args) !== 1) {
            $sender->sendMessage("Usage: §e/seekdr <player>");
            return false;
        }
        $inputName = $args[0];
        $playerData = $this->kdrManager->getPlayerData();
        if (!isset($playerData[$inputName])) {
            $sender->sendMessage("§l§c[!]§r§f Player not found: §e{$inputName}");
            return false;
        }
        $kills = $this->kdrManager->getKills($inputName);
        $deaths = $this->kdrManager->getDeaths($inputName);
        $kdr = ($deaths !== 0) ? round($kills / $deaths, 2) : $kills;
        $formattedName = ucwords($inputName);
        $sender->sendMessage("-----------§e{$formattedName}'s Stats§f-----------");
        $sender->sendMessage("Kills: §e{$kills}");
        $sender->sendMessage("Deaths: §e{$deaths}");
        $sender->sendMessage("KDR: §e{$kdr}");
        return true;
    }
}
