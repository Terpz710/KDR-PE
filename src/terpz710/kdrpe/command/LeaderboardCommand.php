<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

use pocketmine\player\Player;

use terpz710\kdrpe\Core;

use terpz710\kdrpe\utils\Message;

use terpz710\kdrpe\leaderboard\Leaderboard;
use terpz710\kdrpe\leaderboard\LeaderboardType;
use terpz710\kdrpe\leaderboard\LeaderboardException;

class LeaderboardCommand extends KDRCommand {
    
    public function __construct(protected Core $plugin) {
        parent::__construct("leaderboard", $this->plugin);
        $this->setDescription("Fetches the leaderboard for kill, death and killstreak");
        $this->setUsage("/leaderboard <type>");
        $this->setPermission("kdrpe.leaderboard");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }
        
        if (!isset($args[0])) {
            throw new InvalidCommandSyntaxException();
        }
        
        if (!is_string($args[0])) {
            $sender->sendMessage("");
            return;
        }
        
        if (is_numeric($args[0])) {
            $sender->sendMessage("");
            return;
        }

        if (!LeaderboardType::validateType($args[0])) {
            $sender->sendMessage("");
            return;
        }
        
        $type = $this->matchType($args[0]);
        $lb = new Leaderboard($type);
        
        $lb->buildLeaderboard($sender);
    }
    
    private function matchType(string $type) : LeaderboardType{
        return match ($type) {
            "unknown", "KNOWN", "Unknown" => LeaderboardType::UNKNOWN,
            "kill", "KILL", "Kill" => LeaderboardType::KILL,
            "death", "DEATH", "Death" => LeaderboardType::DEATH,
            "killstreak", "KILLSTREAK", "Killstreak" => LeaderboardType::KILLSTREAK,
            default => throw new LeaderboardException("Unknown leaderboard type: " . $type)
        };
    }
}