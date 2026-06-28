<?php

declare(strict_types=1);

namespace terpz710\kdrpe\event;

class AddKillEvent extends KDREvent {
    
    public function __construct(string $name) {
        parent::__construct($name);
    }
}