<?php

declare(strict_types=1);

namespace terpz710\kdrpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

use terpz710\kdrpe\database\Database;

class EventListener implements Listener {
    
    public function onUserLogin(PlayerLoginEvent $event) : void{
        $player = $event->getPlayer();
        $database = Database::getInstance();
        
        if ($database->isNew($player)) {
            $database->insertIntoDatabase($player);
        }
    }
}