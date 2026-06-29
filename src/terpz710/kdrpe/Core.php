<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use terpz710\kdrpe\command\StatsCommand;
use terpz710\kdrpe\command\OtherStatsCommand;
use terpz710\kdrpe\command\LeaderboardCommand;
use terpz710\kdrpe\command\FTLeaderboardCommand;

use terpz710\kdrpe\database\Database;

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
        
        $server->getPluginManager()->registerEvents(new EventListener(), $this);
        
        $server->getCommandMap()->registerAll("KDR-PE", [
            new StatsCommand($this),
            new OtherStatsCommand($this),
            new LeaderboardCommand($this),
            new FTLeaderboardCommand($this)
        ]);
    }
    
    protected function onDisable() : void{
        Database::getInstance()->close();
    }
    
    public static function getInstance() : self{
        return self::$instance;
    }
}