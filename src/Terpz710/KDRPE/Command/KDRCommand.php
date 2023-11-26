<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\KDRPE\Main;

class KDRCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct('kdr', 'Shows your KDR', '/kdr');
        $this->setPermission('kdr-pe.kdr');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $playerName = $sender->getName();
            $kills = $this->plugin->getKills($playerName);
            $deaths = $this->plugin->getDeaths($playerName);

            $kdr = ($deaths === 0) ? $kills : round($kills / $deaths, 2);

            $sender->sendMessage("§l§eKills§f: {$kills}");
            $sender->sendMessage("§l§eDeaths§f: {$deaths}");
            $sender->sendMessage("§l§eKDR§f: {$kdr}");
            
            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return false;
    }
}
