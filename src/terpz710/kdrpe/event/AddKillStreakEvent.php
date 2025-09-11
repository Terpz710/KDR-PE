<?php

declare(strict_types=1);

namespace terpz710\kdrpe\event;

use pocketmine\event\Event;

class AddKillStreakEvent extends KDREvent {

    public function __construct(protected $name) {

    }

    public function getName() : string{
        return $this->name;
    }
}