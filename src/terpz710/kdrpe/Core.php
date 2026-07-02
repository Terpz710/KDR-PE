<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use terpz710\kdrpe\command\StatsCommand;
use terpz710\kdrpe\command\OtherStatsCommand;
use terpz710\kdrpe\command\LeaderboardCommand;
use terpz710\kdrpe\command\FTextLeaderboardCommand;

use terpz710\kdrpe\database\Database;

use terpz710\kdrpe\utils\Utils;

use CortexPE\Commando\PacketHooker;

class Core extends PluginBase {
    
    protected static self $instance;
    
    public Config $messages;
    
    protected function onLoad() : void{
        self::$instance = $this;
    }
    
    protected function onEnable() : void{
        $server = $this->getServer();
        
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        
        $this->messages = new config($this->getDatafolder() . "messages.yml");

        Utils::checkConfigVersions();
        Utils::checkPluginUpdate();
        
        $server->getPluginManager()->registerEvents(new EventListener(), $this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        
        $server->getCommandMap()->registerAll("KDR-PE", [
            new StatsCommand($this, "kdr", "Checkout your current KDR stats"),
            new OtherStatsCommand($this, "seekdr", "Checkout someone else's current KDR stats"),
            new LeaderboardCommand($this, "leaderboard", "Fetches the leaderboard for kill, death and killstreak", ["lb"]),
            new FTextLeaderboardCommand($this, "ftleaderboard", "Spawns in a floating text displaying different leaderboards", ["ftlb"])
        ]);
    }
    
    protected function onDisable() : void{
        Database::getInstance()->close();
    }
    
    public static function getInstance() : self{
        return self::$instance;
    }
}