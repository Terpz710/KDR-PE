<?php

declare(strict_types=1);

namespace terpz710\kdrpe\command;

use pocketmine\command\Command;

use pocketmine\plugin\PluginOwned;

use terpz710\kdrpe\Core;

abstract class KDRCommand extends Command implements PluginOwned {
    
    private Core $plugin;
    
    public function __construct(string $name, Core $plugin) {
        parent::__construct($name);
        $this->plugin = $plugin;
        $this->usageMessage = "";
    }
    
    public function getOwningPlugin() : Core{
        return $this->plugin;
    }
}