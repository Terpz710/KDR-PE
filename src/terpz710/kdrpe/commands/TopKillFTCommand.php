<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\utils\FloatingText;
use terpz710\kdrpe\Main;

class TopKillFTCommand extends Command implements PluginOwned {

    private $plugin;
    private $kdrManager;

    public function __construct() {
        parent::__construct('topkillfloatingtext', 'Show Top Kill leaderboard as Floating Text', '/topkillfloatingtext', ["topkillft", "tkft", "kdrft"]);
        $this->setPermission('kdrpe.command.topkillft');
        $this->plugin = Main::getInstance();
        $this->kdrManager = Main::getInstance()->getKdrManager();
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage('This command can only be used in-game!');
            return true;
        }

        if (!$this->testPermission($sender)) {
            return true;
        }

        $topKillData = $this->kdrManager->getTopKills();

        $text = "-----------§eTOP KILLS§f-----------\n";
        $position = $sender->getPosition();

        $rank = 1;
        foreach ($topKillData as $playerName => $kills) {
            $text .= "§e{$rank}. §f{$playerName}: §e{$kills}\n";
            $rank++;

            if ($rank > 10) {
                break;
            }
        }
        
        $tag = 'topkill';
        FloatingText::create($position, $tag, $text);
        FloatingText::saveToFile($this->plugin->getDataFolder());
        $sender->sendMessage('§l§a[!]§r§f Top Kill leaderboard Floating Text created!');
        return true;
    }
}
