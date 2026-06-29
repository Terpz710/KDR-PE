<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboard;

use pocketmine\player\Player;

use terpz710\kdrpe\api\KDR;

use terpz710\kdrpe\utils\Message;

class Leaderboard {
    
    private string $type;

    public const FTEXT_ID_KILL = "0x01";
    public const FTEXT_ID_KILLSTREAK = "0x02";
    public const FTEXT_ID_DEATH = "0x03";
    
    public function __construct(string $type = LeaderboardType::UNKNOWN) {
        $this->type = $type;
    }
    
    public function buildLeaderboard(Player $player) : void{
        $api = KDR::getInstance();
        
        switch ($this->type) {
            case LeaderboardType::UNKNOWN:
                throw new LeaderboardException("Unknown leaderboard type!");
            
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
                throw new LeaderboardException("Invalid leaderboard type: " . $this->type);
        }
    }
    
    public function buildFloatingText(Player $player) : void{
        $api = KDR::getInstance();
        
        switch ($this->type) {
            case LeaderboardType::UNKNOWN:
                throw new LeaderboardException("Unknown leaderboard type!");

            case LeaderboardType::KILL:
                $top_kills = $api->getTopKills();
                $position = $player->getPosition();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-kill-title", "{line}", "\n");
                
                foreach ($top_kills as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-kill-body",
                        ["{position}", "{player}", "{kills}", "{line}"],
                        [$i, $data["player"], number_format($data["kills"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::create($position, self::FTEXT_ID_KILL, $text);
                $player->sendMessage("FloatingText has successfully spawned in at your position!");
            break;

            case LeaderboardType::KILLSTREAK:
                $top_killstreak = $api->getTopKillstreak();
                $position = $player->getPosition();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-killstreak-title", "{line}", "\n");
                
                foreach ($top_killstreak as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-killstreak-body",
                        ["{position}", "{player}", "{killstreak}", "{line}"],
                        [$i, $data["player"], number_format($data["killstreak"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::create($position, self::FTEXT_ID_KILLSTREAK, $text);
                $player->sendMessage("FloatingText has successfully spawned in at your position!");
            break;

            case LeaderboardType::DEATH:
                $top_deaths = $api->getTopDeaths();
                $position = $player->getPosition();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-death-title", "{line}", "\n");
                
                foreach ($top_deaths as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-death-body",
                        ["{position}", "{player}", "{deaths}", "{line}"],
                        [$i, $data["player"], number_format($data["deaths"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::create($position, self::FTEXT_ID_DEATH, $text);
                $player->sendMessage("FloatingText has successfully spawned in at your position!");
            break;
            
            default:
                throw new LeaderboardException("Invalid leaderboard type: " . $this->type);
        }
    }

    public function updateFloatingText() : void{
        $api = KDR::getInstance();
        
        switch ($this->type) {
            case LeaderboardType::UNKNOWN:
                throw new LeaderboardException("Unknown leaderboard type!");

            case LeaderboardType::KILL:
                $top_kills = $api->getTopKills();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-kill-title", "{line}", "\n");
                
                foreach ($top_kills as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-kill-body",
                        ["{position}", "{player}", "{kills}", "{line}"],
                        [$i, $data["player"], number_format($data["kills"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::update(self::FTEXT_ID_KILL, $text);
            break;

            case LeaderboardType::KILLSTREAK:
                $top_killstreak = $api->getTopKillstreak();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-killstreak-title", "{line}", "\n");
                
                foreach ($top_killstreak as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-killstreak-body",
                        ["{position}", "{player}", "{killstreak}", "{line}"],
                        [$i, $data["player"], number_format($data["killstreak"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::update(self::FTEXT_ID_KILLSTREAK, $text);
            break;

            case LeaderboardType::DEATH:
                $top_deaths = $api->getTopDeaths();
                $i = 1;
            
                $text = (string) new Message("ftext-leaderboard-death-title", "{line}", "\n");
                
                foreach ($top_deaths as $data) {
                    $text .= (string) new Message(
                        "ftext-leaderboard-death-body",
                        ["{position}", "{player}", "{deaths}", "{line}"],
                        [$i, $data["player"], number_format($data["deaths"]), "\n"]
                    );
                    
                    $i++;
                }

                FloatingText::update(self::FTEXT_ID_DEATH, $text);
            break;
            
            default:
                throw new LeaderboardException("Invalid leaderboard type: " . $this->type);
        }
    }
}