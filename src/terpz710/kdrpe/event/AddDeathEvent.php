<?php

declare(strict_types=1);

namespace terpz710\kdrpe\event;

use pocketmine\event\Event;

class AddDeathEvent extends KDREvent {

    public function __construct(protected $name) {

    }

    public function getName() : string{
        return $this->name;
    }
}