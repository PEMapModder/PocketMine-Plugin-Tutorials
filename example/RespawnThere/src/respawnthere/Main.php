<?php

namespace respawnthere;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onRespawn(PlayerRespawnEvent $event){
	}
}
