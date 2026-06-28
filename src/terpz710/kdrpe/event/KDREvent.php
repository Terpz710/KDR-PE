<?php

declare(strict_types=1);

namespace terpz710\kdrpe\event;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

abstract class KDREvent extends Event implements Cancellable {
    use CancellableTrait;
    
    private string $name;
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function getName() : string{
        return $this->name;
    }
}