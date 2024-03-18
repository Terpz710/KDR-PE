<?php

declare(strict_types=1);

namespace Terpz710\KDRPE\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use Terpz710\KDRPE\Main;
use JoJoe77777\FormAPI\SimpleForm;

class TopKillCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct('topkill', 'Shows top kills', '/topkill');
        $this->setPermission('kdr-pe.command.topkill');
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $topKills = $this->plugin->getTopKills();

            if (!empty($topKills)) {
                $content = "-----------\n";
                foreach ($topKills as $playerName => $kills) {
                    $formattedName = ucwords(strtolower($playerName));
                    $content .= "§e{$formattedName}§f: §e{$kills}\n";
                }
                $content .= "-----------";

                $form = new SimpleForm(function (Player $player, ?int $data) use ($content) {
                });
                $form->setTitle("Top Kills");
                $form->setContent($content);
                $sender->sendForm($form);
            } else {
                $sender->sendMessage("§l§f(§c!§f)§r§f No top kills yet!"); //This message shouldnt get sent either way unless the json gets currupted.
            }
        } else {
            $sender->sendMessage("§cThis command can only be used in-game.");
        }

        return true;
    }
}
