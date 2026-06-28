<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboard;

use pocketmine\player\Player;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\utils\Message;

class Leaderboard {
    
    private string $type;
    
    public function __construct(string $type = LeaderboardType::UNKNOWN) {
        $this->type = $type;
    }
    
    public function buildLeaderboard(Player $player) : void{
        $api = KDR::getInstance();
        
        switch ($this->type) {
            case LeaderboardType::UNKNOWN:
                throw new LeaderboardException("Unknown leaderboard type!");
            break;
            
            case LeaderboardType::KILL:
                $top_kills = $api->getTopKills();
                $i = 1;
                
                $player->sendMessage((string) new Message("leaderboard-kill-title"));
                
                foreach ($top_kills as $data) {
                    $player->sendMessage((string) new Message(
                        "leaderboard-kill-body",
                        ["{position}", "{player}", "{kills}"],
                        [$i, $data["player"], number_format($data["kills"])]
                    ));
                    $i++;
                }
            break;
            
            case LeaderboardType::KILLSTREAK:
                $top_killstreak = $api->getTopKillstreak();
                $i = 1;
                
                $player->sendMessage((string) new Message("leaderboard-killstreak-title"));
                
                foreach ($top_killstreak as $data) {
                    $player->sendMessage((string) new Message(
                        "leaderboard-killstreak-body",
                        ["{position}", "{player}", "{killstreak}"],
                        [$i, $data["player"], number_format($data["killstreak"])]
                    ));
                    $i++;
                }
            break;
            
            case LeaderboardType::DEATH:
                $top_deaths = $api->getTopDeaths();
                $i = 1;
                
                $player->sendMessage((string) new Message("leaderboard-death-title"));
                
                foreach ($top_deaths as $data) {
                    $player->sendMessage((string) new Message(
                        "leaderboard-death-body",
                        ["{position}", "{player}", "{deaths}"],
                        [$i, $data["player"], number_format($data["deaths"])]
                    ));
                    $i++;
                }
            break;
            
            default:
                throw new LeaderboardException("Invalid leaderboard type: " . $type);
            break;
        }
    }
    
    public function buildFloatingText() : void{
        //TODO!
        /**switch ($this->type) {
            case LeaderboardType::UNKNOWN:
                return;
            break;
            
            default:
                return;
            break;
        }*/
    }
}