<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\plugin\PluginBase;

use terpz710\kdrpe\command\KDRCommand;
use terpz710\kdrpe\command\SeeKDRCommand;
use terpz710\kdrpe\command\leaderboard\TopKillCommand;
use terpz710\kdrpe\command\leaderboard\TopDeathCommand;
use terpz710\kdrpe\command\leaderboard\TopKillStreakCommand;
use terpz710\kdrpe\command\leaderboard\floatingtext\KillLeaderboardCommand;
use terpz710\kdrpe\command\leaderboard\floatingtext\DeathLeaderboardCommand;
use terpz710\kdrpe\command\leaderboard\floatingtext\KillStreakLeaderboardCommand;

use CortexPE\Commando\PacketHooker;

class Main extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        if(!PacketHooker::isRegistered()){
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("KDR-PE", [
            new KDRCommand($this, "kdr", "View your KDR stats"),
            new SeeKDRCommand($this, "seekdr", "View another player's KDR stats even if they're offline"),
            new TopKillCommand($this, "topkill", "View the top players with the most kills"),
            new TopDeathCommand($this, "topdeath", "View the top players with the most deaths"),
            new TopKillStreakCommand($this, "topkillstreak", "View the top players with the most killstreak"),
            new KillLeaderboardCommand($this, "killleaderboard", "Spawn a floating text showing the top killers", ["klb"]),
            new DeathLeaderboardCommand($this, "deathleaderboard", "Spawn a floating text showing the top deaths", ["dlb"]),
            new KillStreakLeaderboardCommand($this, "killstreaklb", "Spawn a floating text showing the top killerstreak")
        ]);
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}
