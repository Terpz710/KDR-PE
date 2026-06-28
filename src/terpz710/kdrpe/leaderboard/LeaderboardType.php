<?php

declare(strict_types=1);

namespace terpz710\kdrpe\leaderboard;

class LeaderboardType {
    
    public const UNKNOWN = "kdrpe:unknown";
    public const KILL = "kdrpe:kill";
    public const KILLSTREAK = "kdrpe:killstreak";
    public const DEATH = "kdrpe:deaths";
    
    private function __construct() {
        //nada
    }
    
    public static function validateType(self|string $type) : bool{
        return match ($type) {
            self::UNKNOWN, "UNKNOWN", "unknown", "Unknown" => true,
            self::KILL, "KILL", "kill", "Kill" => true,
            self::KILLSTREAK, "KILLSTREAK", "killstreak", "Killstreak" => true,
            self::DEATH, "DEATH", "death", "Death" => true,
            default => throw new LeaderboardException("Unknown leaderboard type: " . $type);
        };
    }
}