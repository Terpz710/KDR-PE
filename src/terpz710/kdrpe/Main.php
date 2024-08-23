<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\plugin\PluginBase;

use terpz710\kdrpe\utils\KdrManager;
use terpz710\kdrpe\utils\FloatingText;

use terpz710\kdrpe\listener\KdrEvent;

use terpz710\kdrpe\commands\KDRCommand;
use terpz710\kdrpe\commands\SeeKDRCommand;
use terpz710\kdrpe\commands\TopKillCommand;
use terpz710\kdrpe\commands\TopKillFTCommand;
use terpz710\kdrpe\commands\KillStreakCommand;
use terpz710\kdrpe\commands\SeeKillStreakCommand;

class Main extends PluginBase {

    private static $instance;
    private $kdrManager;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->kdrManager = new KdrManager();

        $this->getServer()->getCommandMap()->registerAll("KDR-PE", [
            new KDRCommand(),
            new SeeKDRCommand(),
            new TopKillCommand(),
            new TopKillFTCommand(),
            new KillStreakCommand(),
            new SeeKillStreakCommand()
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new KdrEvent(), $this);

        $kdrFolderPath = $this->getDataFolder() . 'KDR';
        if (!is_dir($kdrFolderPath)) {
        @mkdir($kdrFolderPath);
        }

        $dataPath = $kdrFolderPath . DIRECTORY_SEPARATOR . 'data.json';
        if (!file_exists($dataPath)) {
            $initialData = [];
            file_put_contents($dataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }

        $ftDataPath = $this->getDataFolder() . DIRECTORY_SEPARATOR . 'floating_text_data.json';
        if (!file_exists($ftDataPath)) {
            $initialData = [];
            file_put_contents($ftDataPath, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }

    protected function onDisable() : void{
        FloatingText::saveFile();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getKdrManager() : KdrManager{
        return $this->kdrManager;
    }
}
