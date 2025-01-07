<?php

declare(strict_types=1);

namespace terpz710\kdrpe\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use terpz710\kdrpe\Main;

class SeeKDRCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("seekdr");
        $this->setDescription("View another player's KDR stats");
        $this->setUsage("Usage: /seekdr <player>");
        $this->setPermission("kdrpe.seekdr");

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }

        $targetName = $args[0];
        $kdrManager = Main::getInstance()->getKDRManager();

        $uuid = null;
        foreach ($kdrManager->data->getAll() as $storedUuid => $data) {
            if ($data["username"] === $targetName) {
                $uuid = $storedUuid;
                break;
            }
        }

        if ($uuid === null) {
            $sender->sendMessage(TextFormat::RED . $targetName . " does not exist!");
            return false;
        }

        $kills = $kdrManager->data->get($uuid)["kills"];
        $deaths = $kdrManager->data->get($uuid)["deaths"];
        $killstreak = $kdrManager->data->get($uuid)["killstreak"];
        $kdr = ($deaths === 0) ? $kills : round($kills / $deaths, 2);

        $sender->sendMessage("§l=====§e {$targetName}'s KDR stats §f=====");
        $sender->sendMessage("kills:§e $kills");
        $sender->sendMessage("deaths:§e $deaths");
        $sender->sendMessage("killstreak:§e $killstreak");
        $sender->sendMessage("KDR:§e $kdr");
        $sender->sendMessage("§l===========================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
