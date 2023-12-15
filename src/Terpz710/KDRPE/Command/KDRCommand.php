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

            $formattedName = ucwords(strtolower($playerName));
            $sender->sendMessage("-----------§e{$formattedName}'s Stats§f-----------");
            $sender->sendMessage("Kills: §e{$kills}");
            $sender->sendMessage("Deaths: §e{$deaths}");
            $sender->sendMessage("KDR: §e{$kdr}");
            
            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return false;
    }
}
