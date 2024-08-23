<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

use terpz710\kdrpe\Main;

class TopKillCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct('topkill', 'Shows top kills', '/topkill');
        $this->setPermission('kdr-pe.command.topkill');
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        $topKills = $this->kdrManager->getTopKills();

        if (!empty($topKills)) {
            $sender->sendMessage("-----------§eTop Kills§f-----------");

            $rank = 1;
            foreach ($topKills as $playerName => $kills) {
                $formattedName = ucwords(strtolower($playerName));
                $sender->sendMessage("§e{$rank}. §f{$formattedName}§f: §e{$kills}");
                $rank++;
                if ($rank > 10) {
                    break;
                }
            }
        } else {
            $sender->sendMessage("§l§c[!]§r§f No top kills yet!");
        } //This message shouldnt get sent either way unless the json gets currupted...
        return true;
    }
}
