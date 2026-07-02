<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\kdrpe\utils\Message;

use terpz710\kdrpe\leaderboard\Leaderboard;
use terpz710\kdrpe\leaderboard\LeaderboardType;
use terpz710\kdrpe\leaderboard\LeaderboardException;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\TextArgument;

class LeaderboardCommand extends BaseCommand {
    
    protected function prepare() : void{
        $this->registerArgument(0, new TextArgument("type"));
        $this->setPermission("kdrpe.leaderboard");
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }

        if (!LeaderboardType::validateType($args["type"])) {
            $this->availableTypes($sender);
            return;
        }

        $type = $this->matchType($args["type"]);

        if ($type === null) {
            $this->availableTypes($sender);
            return;
        }
        
        $lb = new Leaderboard($type);
        
        $lb->buildLeaderboard($sender);
    }

    private function matchType(string $type) : ?string{
        return match ($type) {
            "unknown", "UNKNOWN", "Unknown" => LeaderboardType::UNKNOWN,
            "kill", "KILL", "Kill" => LeaderboardType::KILL,
            "death", "DEATH", "Death" => LeaderboardType::DEATH,
            "killstreak", "KILLSTREAK", "Killstreak" => LeaderboardType::KILLSTREAK,
            default => null
        };
    }

    private function availableTypes(Player $player) : void{
        $player->sendMessage("Unknown leaderboard type!");
        $player->sendMessage("");
        $player->sendMessage("Available types:");
        $player->sendMessage("kill, killstreak and death");
    }
}