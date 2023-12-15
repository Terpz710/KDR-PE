<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use Terpz710\KDRPE\Main;

class SeeKDRCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct('seekdr', 'See other players KDR', '/seekdr <player>');
        $this->setPermission("kdr-pe.command.seekdr");
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$this->plugin->isEnabled()) {
            return false;
        }
        if (count($args) !== 1) {
            $sender->sendMessage("Usage: §e/seekdr <player>");
            return false;
        }
        $inputName = $args[0];
        $playerData = $this->plugin->getPlayerData();
        if (!isset($playerData[$inputName])) {
            $sender->sendMessage("§l§f(§c!§f)§r§f Player not found: §e{$inputName}");
            return false;
        }
        $kills = $this->plugin->getKills($inputName);
        $deaths = $this->plugin->getDeaths($inputName);
        $kdr = ($deaths !== 0) ? round($kills / $deaths, 2) : $kills;
        $formattedName = ucwords($inputName);
        $sender->sendMessage("-----------§e{$formattedName}'s Stats§f-----------");
        $sender->sendMessage("Kills: §e{$kills}");
        $sender->sendMessage("Deaths: §e{$deaths}");
        $sender->sendMessage("KDR: §e{$kdr}");
        return true;
    }
}