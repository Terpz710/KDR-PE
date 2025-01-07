<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\plugin\PluginBase;

use terpz710\kdrpe\commands\KDRCommand;
use terpz710\kdrpe\commands\SeeKDRCommand;
use terpz710\kdrpe\commands\leaderboards\TopKillCommand;
use terpz710\kdrpe\commands\leaderboards\TopDeathCommand;
use terpz710\kdrpe\commands\leaderboards\TopKillStreakCommand;
use terpz710\kdrpe\commands\leaderboards\floatingtext\KillLeaderboardCommand;
use terpz710\kdrpe\commands\leaderboards\floatingtext\DeathLeaderboardCommand;
use terpz710\kdrpe\commands\leaderboards\floatingtext\KillStreakLeaderboardCommand;

use terpz710\kdrpe\manager\KDRManager;

use terpz710\kdrpe\scorehud\KDRScoreHud;

use terpz710\kdrpe\floatingtext\FloatingText;

final class Main extends PluginBase {

    protected static self $instance;
    
    public KDRManager $manager;
    public KDRScoreHud $scorehud;

    protected function onLoad() : void{ 
        self::$instance = $this;
    }

    protected function onEnable() : void{ 
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getCommandMap()->registerAll("KDR-PE", [
            new KDRCommand(),
            new SeeKDRCommand(),
            new TopKillCommand(),
            new TopDeathCommand(),
            new KillLeaderboardCommand(),
            new DeathLeaderboardCommand(),
            new KillStreakLeaderboardCommand(),
            new TopKillStreakCommand()
        ]);

        $this->manager = new KDRManager();
        $this->scorehud = new KDRScoreHud();
    }

    protected function onDisable() : void{
        FloatingText::saveFile();
    }

    public static function getInstance() : self{ 
        return self::$instance; 
    }

    public function getKDRManager() : KDRManager{ 
        return $this->manager; 
    }

    public function getKDRScoreHud() : KDRScoreHud{ 
        return $this->scorehud; 
    }
}