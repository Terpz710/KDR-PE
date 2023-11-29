<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\KDRPE\Main;

class TopKillCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct('topkill', 'Shows top kills', '/topkill');
        $this->setPermission('kdr-pe.topkill');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $topKills = $this->plugin->getTopKills();

        if (!empty($topKills)) {
            $sender->sendMessage("Top Kills:");

            foreach ($topKills as $playerName => $kills) {
                $sender->sendMessage("{$playerName}: {$kills}");
            }
        } else {
            $sender->sendMessage("No top kills yet.");
        }

        return true;
    }
}
