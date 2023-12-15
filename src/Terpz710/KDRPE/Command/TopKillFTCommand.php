<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use Terpz710\KDRPE\API\FloatingKDRAPI;
use Terpz710\KDRPE\Main;

class TopKillFTCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct('topkillfloatingtext', 'Show Top Kill leaderboard as Floating Text', '/topkillfloatingtext');
        $this->setPermission('kdr-pe.topkillft');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage('This command can only be used in-game!');
            return true;
        }

        if (!$this->testPermission($sender)) {
            return true;
        }

        if (empty($args)) {
            $sender->sendMessage('Usage: /topkillfloatingtext [on|off]');
            return true;
        }

        $subCommand = strtolower($args[0]);
        if ($subCommand === 'on') {
            $this->showFloatingText($sender);
            $sender->sendMessage('§l(§a!§f)§r§f Top Kill leaderboard Floating Text created!');
        } elseif ($subCommand === 'off') {
            $this->removeFloatingText($sender);
            $sender->sendMessage('§l(§c!§r§f) Top Kill leaderboard Floating Text removed!');
        } else {
            $sender->sendMessage('Usage: /topkillfloatingtext [on|off]');
        }

        return true;
    }

    private function showFloatingText(Player $player) {
        $topKillData = $this->plugin->getTopKills();

        $text = "-----------§eTOP KILLS§f-----------\n";
        $position = $player->getPosition();

        $rank = 1;
        foreach ($topKillData as $playerName => $kills) {
            $text .= "§e{$rank}. §f{$playerName}: §e{$kills}\n";
            $rank++;

            if ($rank > 10) {
                break;
            }
        }

        $tag = 'topkill';
        FloatingKDRAPI::create($position, $tag, $text, $this->plugin->getDataFolder());
        FloatingKDRAPI::saveToFile($this->plugin->getDataFolder() . 'FT' . DIRECTORY_SEPARATOR . 'floating_text_data.json');
        return true;
    }

    private function removeFloatingText(Player $player) {
        $tag = 'topkill';
        FloatingKDRAPI::remove($tag, $this->plugin->getDataFolder() . 'FT');
        FloatingKDRAPI::saveToFile($this->plugin->getDataFolder() . 'FT' . DIRECTORY_SEPARATOR . 'floating_text_data.json');
        return true;
    }
}
