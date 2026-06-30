<?php

declare(strict_types=1);

namespace terpz710\kdrscoretag;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;

class Core extends PluginBase {

    protected function onEnable() : void{
        $server = $this->getServer();
        $manager = $server->getPluginManager();
        $kdrpe = $manager->getPlugin("KDR-PE");
        $scorehud = $manager->getPlugin("ScoreHud");

        if ($kdrpe === null || !$kdrpe->isEnabled()) {
            throw new PluginException("Plugin 'KDR-PE' is not installed, Please download the latest phar from https://poggit.pmmp.io/p/KDR-PE");
        }

        if ($scorehud === null || !$scorehud->isEnabled()) {
            throw new PluginException("Plugin 'KDR-PE' is not installed, Please download the latest phar from https://poggit.pmmp.io/p/ScoreHud");
        }

        $manager->registerEvents(new EventListener(), $this);
    }
}