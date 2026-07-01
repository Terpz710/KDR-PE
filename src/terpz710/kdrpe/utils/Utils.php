<?php

declare(strict_types=1);

namespace terpz710\kdrpe\utils;

use pocketmine\utils\Config;

use terpz710\kdrpe\Core;

use JackMD\UpdateNotifier\UpdateNotifier;

use JackMD\ConfigUpdater\ConfigUpdater;

class Utils {

    public const CONFIG_VERSION = 1;
    public const MESSAGE_VERSION = 1;
    
    public static function checkConfigVersions() : void{
        $core = Core::getInstance();
        $configVersion = $core->getConfig()->get("config-version");
        
        ConfigUpdater::checkUpdate($core, $core->getConfig(), "config-version", self::CONFIG_VERSION);
        ConfigUpdater::checkUpdate($core, $core->messages, "message-version", self::MESSAGE_VERSION);
    }

    public static function checkPluginUpdate() : void{
        $core = Core::getInstance();
        
        UpdateNotifier::checkUpdate($core->getName(), $core->getVersion());
    }
}